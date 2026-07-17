import paramiko
import os

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'
remote_theme = '/www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Download front-page.php
stdin, stdout, stderr = ssh.exec_command('cat %s/front-page.php' % remote_theme)
content = stdout.read().decode()

# Save locally
local_path = r'D:\claud code项目\aiphoto-theme\front-page.php'
with open(local_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Downloaded front-page.php: %d chars" % len(content))
print("Saved to: %s" % local_path)

# Show the generate section boundaries
lines = content.split('\n')
for i, line in enumerate(lines):
    if "elseif ( 'generate' === \$page )" in line:
        print("Generate section STARTS at line %d: %s" % (i+1, line[:80]))
    if i > 347 and "elseif ( 'gallery' === \$page )" in line:
        print("Generate section ENDS before line %d: %s" % (i+1, line[:80]))
        break

ssh.close()
