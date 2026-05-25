$userCount = App\Models\User::count();
$donorCount = App\Models\User::where('role', 'donor')->count();
$goldCount = App\Models\User::where('priority_tier', 'gold')->count();
$silverCount = App\Models\User::where('priority_tier', 'silver')->count();
$dfiAvg = App\Models\User::where('role', 'donor')->avg('dfi_score');
$healthRecordCount = App\Models\HealthRecord::count();
$donationCount = App\Models\Donation::count();
$deliveredCount = App\Models\Donation::where('journey_status', 'delivered')->count();
$discardedCount = App\Models\Donation::where('journey_status', 'discarded')->count();

echo "=== ENTERPRISE SIMULATION VERIFICATION ===\n";
echo "Total Users: $userCount\n";
echo "Total Donors: $donorCount\n";
echo "Gold Tier: $goldCount | Silver Tier: $silverCount\n";
echo "Avg DFI Score: " . number_format($dfiAvg, 2) . "\n";
echo "Health Records: $healthRecordCount\n";
echo "Donations: $donationCount (Delivered: $deliveredCount, Discarded: $discardedCount)\n";
