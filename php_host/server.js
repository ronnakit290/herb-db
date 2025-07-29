const http = require('http');
const fs = require('fs');
const path = require('path');

const server = http.createServer((req, res) => {
    // Simple static file server for demonstration
    let filePath = path.join(__dirname, req.url === '/' ? 'index.php' : req.url);
    
    // Check if file exists
    if (fs.existsSync(filePath)) {
        const ext = path.extname(filePath);
        let contentType = 'text/html';
        
        if (ext === '.css') contentType = 'text/css';
        if (ext === '.js') contentType = 'application/javascript';
        if (ext === '.php') contentType = 'text/html';
        
        fs.readFile(filePath, 'utf8', (err, content) => {
            if (err) {
                res.writeHead(500);
                res.end('Server Error');
                return;
            }
            
            res.writeHead(200, { 'Content-Type': contentType });
            res.end(content);
        });
    } else {
        res.writeHead(404);
        res.end('File not found');
    }
});

const PORT = 8000;
server.listen(PORT, () => {
    console.log(`Server running at http://localhost:${PORT}`);
    console.log('Note: This is a simple file server. PHP files will be served as plain text.');
    console.log('For full PHP functionality, please install PHP and use: php -S localhost:8000');
});