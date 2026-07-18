const fs = require('fs');

// Read and parse package.json
const packageJson = JSON.parse(fs.readFileSync('./package.json', 'utf-8'));
const dependencies = Object.keys(packageJson.dependencies).concat(Object.keys(packageJson.devDependencies));

// Filter out grunt dependencies
const gruntDependencies = dependencies.filter(name => name.startsWith('grunt-'));

// Read Gruntfile.js
const gruntfileContent = fs.readFileSync('./Gruntfile.js', 'utf-8');

// Check which grunt dependencies are not required in Gruntfile.js
const unusedGruntDependencies = gruntDependencies.filter(name => !gruntfileContent.includes(name));

console.log('Unused Grunt Dependencies:', unusedGruntDependencies);
