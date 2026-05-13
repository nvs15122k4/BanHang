const fs = require('fs');
const path = require('path');

const viewsDir = '/Ubuntu/home/nvs1512/BanHang/resources/views';

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(file => {
        file = dir + '/' + file;
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) { 
            results = results.concat(walk(file));
        } else if (file.endsWith('.blade.php')) {
            results.push(file);
        }
    });
    return results;
}

const files = walk(viewsDir);

const replacements = [
    { from: /rgba\(15, 23, 42,/g, to: 'rgba(255, 255, 255,' },
    { from: /rgba\(30, 41, 59,/g, to: 'rgba(240, 240, 240,' },
    { from: /rgba\(0,0,0,0\.2\)/g, to: 'rgba(0,0,0,0.03)' },
    { from: /rgba\(0,0,0,0\.3\)/g, to: 'rgba(0,0,0,0.05)' },
    { from: /rgba\(255,255,255,0\.05\)/g, to: 'rgba(0,0,0,0.05)' },
    { from: /rgba\(255,255,255,0\.1\)/g, to: 'rgba(0,0,0,0.1)' },
    { from: /rgba\(255,255,255,0\.2\)/g, to: 'rgba(0,0,0,0.2)' },
    { from: /rgba\(255,255,255,0\.3\)/g, to: 'rgba(0,0,0,0.3)' },
    { from: /rgba\(255,255,255,0\.4\)/g, to: 'rgba(0,0,0,0.4)' },
    { from: /color: #fff;/g, to: 'color: var(--text-main);' },
    { from: /color: #FFFFFF;/g, to: 'color: var(--text-main);' },
    { from: /color:#fff;/g, to: 'color: var(--text-main);' },
    { from: /color:#FFFFFF;/g, to: 'color: var(--text-main);' },
    { from: /background: transparent;/g, to: 'background: transparent;' }, // noop
];

let changedFiles = 0;
for (const file of files) {
    let content = fs.readFileSync(file, 'utf8');
    let original = content;
    
    // We only want to apply this to the files we recently edited with the Neon Aurora theme.
    if (!content.includes('font-outfit') && !content.includes('neon') && !file.includes('app.blade.php')) {
        continue; // Skip untouched files to avoid breaking old stuff
    }

    for (const r of replacements) {
        content = content.replace(r.from, r.to);
    }
    
    // Some specific fixes
    content = content.replace(/btn-close-white/g, ''); // Use dark close button
    content = content.replace(/filter: invert\(1\);/g, ''); // Close button color fix
    
    if (content !== original) {
        fs.writeFileSync(file, content);
        console.log(`Modified: ${file}`);
        changedFiles++;
    }
}

console.log(`Done. Changed ${changedFiles} files.`);
