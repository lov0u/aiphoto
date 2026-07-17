import hashlib

local_files = [
    r'D:\claud code项目\aiphoto-theme\page-generate.php',
    r'D:\claud code项目\aiphoto-theme\assets\css\page-styles.css',
    r'D:\claud code项目\aiphoto-theme\assets\js\main.js',
]

for f in local_files:
    with open(f, 'rb') as fh:
        md5 = hashlib.md5(fh.read()).hexdigest()
    print("%s: %s" % (f.split('\\')[-1], md5))
