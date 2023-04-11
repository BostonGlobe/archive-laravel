## Archive Project, using Laravel

The goal of this proof of concept (POC) project is to creae a simple PHP app that can parse obsolete HTML files, update them with new template and serve them.

It uses the Laravel framework to provide a secure, extensible, and maintainable codebase.

The key files here used by this app are:

- /routes/web.php
- /resources/views/template.blade.php
- /app/Models/Article.php

Styling is done using Bootstrap and Sass. The root source stylesheet is at

- /resources/css/app.scss

To set up the project run the following commands:

1. `composer install`. This installs PHP dependencies.
2. `docker compose up`. This sets up the containers that run the PHP. 
3. `npm install`. This installs Bootstrap and Vite (CSS and JavaScript bundler).
4. `npm run dev`. This will watch and compile the SASS.

The site should be viewable at http//localhost/
