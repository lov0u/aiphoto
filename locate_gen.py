import paramiko
import base64
import os

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'
remote_theme = '/www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Read the current front-page.php from server
stdin, stdout, stderr = ssh.exec_command('cat %s/front-page.php' % remote_theme)
content = stdout.read().decode()

# Save locally for editing
local_path = r'D:\claud code项目\aiphoto-theme\front-page.php'
with open(local_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Downloaded front-page.php: %d chars, %d lines" % (len(content), content.count('\n')))

# Find generate section boundaries precisely
lines = content.split('\n')
gen_start_line = None  # line index of "<?php elseif ( 'generate' === $page ) : ?>"
gen_end_line = None    # line index before "<?php elseif ( 'gallery' === $page ) : ?>"

for i, line in enumerate(lines):
    if "<?php elseif ( 'generate' === $page ) :" in line and gen_start_line is None:
        gen_start_line = i
    if gen_start_line is not None and "<?php elseif ( 'gallery' === $page ) :" in line:
        gen_end_line = i
        break

print("Generate section: lines %d-%d (0-indexed)" % (gen_start_line, gen_end_line))
print("That's lines %d-%d (1-indexed)" % (gen_start_line+1, gen_end_line))

# Show what's at those boundaries
print("\nBoundary lines:")
print("START: %s" % lines[gen_start_line].strip())
print("END:   %s" % lines[gen_end_line].strip())

# Show surrounding context
print("\nLines before start:")
for i in range(max(0, gen_start_line-2), gen_start_line):
    print("  %d: %s" % (i+1, lines[i][:80]))

print("\nLines at end:")
for i in range(gen_end_line-3, gen_end_line+1):
    print("  %d: %s" % (i+1, lines[i][:80]))

ssh.close()
