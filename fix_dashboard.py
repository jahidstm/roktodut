
import codecs
import re

with codecs.open("resources/views/dashboard.blade.php", "r", "utf-8") as f:
    text = f.read()

idx_Radar = text.find("{{-- 3. Actionable Queue")
idx_Commitments = text.find("{{-- E) My Commitments")
idx_Stats = text.find("{{-- 4. User Impact (Stats Grid)")
idx_History = text.find("{{-- 7. Verified Donation History")
idx_Recent = text.find("@if(isset($recentRequests))")
idx_SmartCard = text.find("{{-- 5. Motivation Engine")
idx_Growth = text.find("{{-- 6. Growth Loop")

radar_block = text[idx_Radar:idx_Commitments]
stats_block = text[idx_Stats:idx_History]

# Reassemble
part1 = text[0:idx_Radar]
part2 = text[idx_Commitments:idx_Stats]
part3 = text[idx_History:idx_Recent]
part4 = stats_block + "\n    " + text[idx_Recent:idx_SmartCard]
part5 = text[idx_SmartCard:idx_Growth]
part6 = radar_block + "\n    " + text[idx_Growth:]

new_text = part1 + part2 + part3 + part4 + part5 + part6

with codecs.open("resources/views/dashboard.blade.php", "w", "utf-8") as f:
    f.write(new_text)

print("Success!")

