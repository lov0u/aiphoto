import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'
remote_theme = '/www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Verify CSS on server has gen-page styles
stdin, stdout, stderr = ssh.exec_command('grep -n "\.gen-page\|\.gen-layout\|\.gen-panel" %s/assets/css/page-styles.css' % remote_theme)
print("=== CSS gen-page rules ===")
print(stdout.read().decode())

# Also check the CSS file size
stdin, stdout, stderr = ssh.exec_command('wc -c < %s/assets/css/page-styles.css' % remote_theme)
print("CSS size:", stdout.read().decode().strip(), "bytes")

# Check what's in the CSS header
stdin, stdout, stderr = ssh.exec_command('head -5 %s/assets/css/page-styles.css' % remote_theme)
print("\nCSS header:")
print(stdout.read().decode())

ssh.close()
