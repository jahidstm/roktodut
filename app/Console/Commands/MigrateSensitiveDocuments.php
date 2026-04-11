<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Organization;

class MigrateSensitiveDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:migrate-sensitive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate sensitive documents (NID and Org Admin documents) from public/local storage to private storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration of sensitive documents to private storage...');

        $privateDisk = Storage::disk('private');

        // 1. NID Documents (previously on 'local' disk, i.e., storage/app/nid_uploads)
        $users = User::whereNotNull('nid_path')->get();
        $nidMoved = 0;
        $nidSkipped = 0;

        foreach ($users as $user) {
            $oldPath = $user->nid_path;

            // Check if already in private
            if ($privateDisk->exists($oldPath)) {
                $this->line("Skipping NID for user {$user->id} - already in private storage.");
                $nidSkipped++;
                continue;
            }

            // Check if exists in old local storage (storage/app)
            if (Storage::disk('local')->exists($oldPath)) {
                $fileContent = Storage::disk('local')->get($oldPath);
                $privateDisk->put($oldPath, $fileContent);
                // We keep the relative path same e.g. nid_uploads/xxx.jpg
                Storage::disk('local')->delete($oldPath);
                $this->info("Moved NID for user {$user->id} to private storage.");
                $nidMoved++;
            } else {
                $this->error("NID file not found for user {$user->id} at {$oldPath}. Skipped.");
                $nidSkipped++;
            }
        }

        // 2. Organization Documents (previously on 'public' disk, i.e., storage/app/public/org_documents)
        $orgs = Organization::whereNotNull('document_path')->get();
        $orgMoved = 0;
        $orgSkipped = 0;

        foreach ($orgs as $org) {
            $oldPath = $org->document_path;

            if ($privateDisk->exists($oldPath)) {
                $this->line("Skipping document for org {$org->id} - already in private storage.");
                $orgSkipped++;
                continue;
            }

            if (Storage::disk('public')->exists($oldPath)) {
                $fileContent = Storage::disk('public')->get($oldPath);
                $privateDisk->put($oldPath, $fileContent);
                Storage::disk('public')->delete($oldPath);
                $this->info("Moved document for org {$org->id} to private storage.");
                $orgMoved++;
            } else {
                $this->error("Document file not found for org {$org->id} at {$oldPath}. Skipped.");
                $orgSkipped++;
            }
        }

        $this->info("Migration completed.");
        $this->line("NIDs - Moved: {$nidMoved}, Skipped: {$nidSkipped}");
        $this->line("Org Docs - Moved: {$orgMoved}, Skipped: {$orgSkipped}");
    }
}
