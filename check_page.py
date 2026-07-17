import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Get the generate page HTML
stdin, stdout, stderr = ssh.exec_command('curl -s -H "Host: aiphoto.ra0.cn" http://127.0.0.1/generate 2>&1 | head -100')
print("=== Generate page HTML (first 100 lines) ===")
content = stdout.read().decode()
lines = content.split('\n')
for i, line in enumerate(lines[:100]):
    print("%3d: %s" % (i+1, line[:120]))

# Also check if the theme file is actually being served
print("\n\n=== Nginx error log (last 20 lines) ===")
stdin, stdout, stderr = ssh.exec_command('tail -20 /www/wwwlogs/aiphoto_error.log 2>/dev/null || tail -20 /www/server/nginx/logs/error.log 2>/dev/null || echo "no error log"')
print(stdout.read().decode())

# Check the actual theme files on server
print("\n\n=== Theme files ===")
stdin, stdout, stderr = ssh.exec_command('ls -la /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/')
print(stdout.read().decode())

# Check if page template is recognized
print("\n=== Checking page template header ===")
stdin, stdout, stderr = ssh.exec_command('head -20 /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/page-generate.php')
print(stdout.read().decode())

ssh.close()
