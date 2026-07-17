import paramiko

host = '120.55.171.1'
username = 'root'
password = 'zhang_LEI758'

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(host, port=22, username=username, password=password, timeout=15)

# Get the generate page and check for Agnes-specific elements
stdin, stdout, stderr = ssh.exec_command('curl -s -H "Host: aiphoto.ra0.cn" http://127.0.0.1/generate 2>&1')
content = stdout.read().decode()

# Check for Agnes-specific class names
checks = [
    ('agnes-global-bg', 'Agnes gradient background'),
    ('agnes-input-card', 'Agnes input card'),
    ('agnes-sidebar', 'Agnes sidebar'),
    ('agnes-welcome', 'Agnes welcome section'),
    ('class="agnes-', 'Any agnes-* class'),
    ('gen-page', 'OLD gen-page class'),
    ('gen-layout', 'OLD gen-layout class'),
    ('gen-panel', 'OLD gen-panel class'),
]

print("=== Page content analysis ===\n")
for keyword, desc in checks:
    count = content.count(keyword)
    status = "NEW" if count > 0 and 'OLD' not in desc else ("OLD" if count > 0 and 'OLD' in desc else "OK")
    print("[%s] %s: %d occurrences" % (status, desc, count))

# Show the main content area
print("\n=== Body content (main section) ===")
start = content.find('<main')
if start >= 0:
    end = content.find('</main>') + len('</main>')
    main_content = content[start:end]
    for i, line in enumerate(main_content.split('\n')[:60]):
        print("%3d: %s" % (i+1, line[:120]))

# Check CSS is being loaded
print("\n=== CSS links in head ===")
for line in content.split('\n'):
    if 'page-styles.css' in line or 'custom.css' in line:
        print("  " + line.strip())

ssh.close()
