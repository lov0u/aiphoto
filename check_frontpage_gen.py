import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'
remote_theme = '/www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Check the generate section in front-page.php
stdin, stdout, stderr = ssh.exec_command("sed -n '347,500p' /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/front-page.php")
content = stdout.read().decode()
print("=== front-page.php generate section (lines 347-500) ===")
for i, line in enumerate(content.split('\n')[:155]):
    print("%3d: %s" % (347+i, line[:120]))

ssh.close()
