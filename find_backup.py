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

# Strategy: Replace the generate section in front-page.php with Agnes-style HTML
# But FIRST, we need to restore the old CSS so the page looks normal again

# Step 1: Check if there's a backup of the original page-styles.css
# The original had .gen-page styles. We need to either restore it or merge.

# Let's first check what the original CSS looked like by examining git or backups
stdin, stdout, stderr = ssh.exec_command('find /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme -name "*.css.bak" -o -name "*.orig" 2>/dev/null')
print("=== Backup CSS files ===")
print(stdout.read().decode())

# Check bt panel backup
stdin, stdout, stderr = ssh.exec_command('ls /www/backup/file_history/www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/assets/css/ 2>/dev/null | head -20')
print("=== BT Panel CSS backup ===")
print(stdout.read().decode())

# Get the most recent backup of page-styles.css
stdin, stdout, stderr = ssh.exec_command('find /www/backup -name "page-styles.css" 2>/dev/null | sort | tail -5')
print("\n=== Backup page-styles.css locations ===")
print(stdout.read().decode())

ssh.close()
