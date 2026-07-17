import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Check if front-page.php got corrupted by our replace attempt
stdin, stdout, stderr = ssh.exec_command('wc -l /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/front-page.php')
print("Line count:", stdout.read().decode().strip())

stdin, stdout, stderr = ssh.exec_command('head -5 /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/front-page.php')
print("First 5 lines:")
print(stdout.read().decode())

# Check for PHP syntax errors
stdin, stdout, stderr = ssh.exec_command('php -l /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/front-page.php 2>&1')
print("PHP lint:")
print(stdout.read().decode())
print(stderr.read().decode())

# Check the beginning of the file to see if it's still valid PHP
stdin, stdout, stderr = ssh.exec_command('head -20 /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/front-page.php')
print("First 20 lines:")
print(stdout.read().decode())

# Check if the file starts with <?php
stdin, stdout, stderr = ssh.exec_command('head -c 20 /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/front-page.php | xxd | head -3')
print("Hex dump of first bytes:")
print(stdout.read().decode())

ssh.close()
