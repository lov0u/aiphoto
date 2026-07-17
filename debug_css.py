import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# 1. Check if the CSS file contains Agnes-specific styles
print("=== Checking page-styles.css for Agnes keywords ===")
stdin, stdout, stderr = ssh.exec_command('grep -c "agnes-global-bg\\|agnes-input-card\\|agnes-sidebar\\|agnes-gradient" /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/assets/css/page-styles.css')
count = stdout.read().decode().strip()
print("Agnes style keywords found: %s" % count)

# 2. Check if the JS file contains LocalStorage logic
print("\n=== Checking main.js for LocalStorage ===")
stdin, stdout, stderr = ssh.exec_command('grep -c "localStorage\\|aiphoto_recent" /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/assets/js/main.js')
count = stdout.read().decode().strip()
print("LocalStorage references found: %s")
stdin, stdout, stderr = ssh.exec_command('grep -n "aiphoto_recent\\|agnes-sidebar" /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/assets/js/main.js | head -5')
print(stdout.read().decode())

# 3. Check page-generate.php for sidebar container
print("\n=== Checking page-generate.php for Agnes elements ===")
stdin, stdout, stderr = ssh.exec_command('grep -n "agnes-sidebar\\|sidebar-container\\|recent-works" /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/page-generate.php | head -10')
print(stdout.read().decode())

# 4. Check if CSS version is being cached
print("\n=== Checking CSS version in page output ===")
stdin, stdout, stderr = ssh.exec_command('grep "page-styles\\.css" /www/wwwroot/aiphoto/wp-content/themes/aiphoto-theme/page-generate.php')
print(stdout.read().decode())

# 5. Get the full body content of the generate page
print("\n=== Full generate page body (after header) ===")
stdin, stdout, stderr = ssh.exec_command('curl -s -H "Host: aiphoto.ra0.cn" http://127.0.0.1/generate 2>&1 | sed -n "/<main/,/<\\/main>/p" | head -80')
result = stdout.read().decode()
for i, line in enumerate(result.split('\n')[:80]):
    print("%3d: %s" % (i+1, line[:120]))

ssh.close()
