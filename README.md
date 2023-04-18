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
4. `npm run dev`. This will watch and compile the SASS. This requires Node V. 16 or greater.

The site should be viewable at http://localhost/, or http://localhost:&lt;port> if your container is mapped to a different port.

Sample URLs to test:

- http://localhost:82/business/articles/2004/03/03/boston_will_host_microsoft_convention_in_2006/index.html
- http://localhost:82/business/articles/2009/03/03/consumer_spending_rises_06_in_january/index.html 
- http://localhost:82/business/articles/2009/03/09/alone_together/index.html
- http://localhost:82/business/technology/articles/2006/08/07/some_local_companies_hoping_to_make_the_grade_in_web_20/index.html
- http://localhost:82/business/articles/2004/03/11/fda_chief_will_not_lead_study_on_drug_imports/index.html
- http://localhost:82/ae/books/harry_potter/potter_beating_the_dickens/index.html
- http://localhost:82/ae/books/articles/2006/08/08/rallies_riots_and_a_radical_response/index.html
  
