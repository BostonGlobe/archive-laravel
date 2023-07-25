## Archive Project, using Laravel

The goal of this proof of concept (POC) project is to creae a simple PHP app that can parse obsolete HTML files, update them with new template and serve them.

It uses the Laravel framework to provide a secure, extensible, and maintainable codebase.

Styling is done using Bootstrap and Sass. The root source stylesheet is at

- /resources/css/app.scss

To set up the project, run the following commands:
1. `composer install`. This installs PHP dependencies.
2. `docker compose up`. This sets up the containers that run the PHP. 
3. `npm install`. This installs Bootstrap and Vite (CSS and JavaScript bundler).
4. `npm run dev`. This will watch and compile the SASS. Requires Node V. 16 or greater.

To index files for ElasticSearch:
1. Open a Terminal in the project root.
2. index a list of URLs in a text list. The list should be in this format:
   /business/articles/2004/05/17/the_ceos_explain_the_urge_to_merge_boston_globe/full.html
   /business/articles/2006/04/12/retail_giant_offers_staffing_data_on_women_minorities/index.html
   /business/articles/2006/04/12/skilling_says_he_didnt_rig_earnings/index.html
   /business/articles/2006/04/12/summer_gas_prices_likely_to_average_262_a_gallon/index.html
3. Index with this command `php artisan index:html {filepath}`

The site should be viewable at http://localhost/, or http://localhost:[port] if your container is mapped to a different port.

Sample URLs to test:
- https://archive-laravel.test/business/articles/2004/03/03/boston_will_host_microsoft_convention_in_2006/index.html
- https://archive-laravel.test/business/articles/2009/03/03/consumer_spending_rises_06_in_january/index.html 
- https://archive-laravel.test/business/articles/2009/03/09/alone_together/index.html
- https://archive-laravel.test/business/articles/2004/03/11/fda_chief_will_not_lead_study_on_drug_imports/index.html
- https://archive-laravel.test/ae/books/harry_potter/potter_beating_the_dickens/index.html
- https://archive-laravel.test/ae/books/articles/2006/08/08/rallies_riots_and_a_radical_response/index.html
  
TODOs:
1. Create logic to avoid indexing duplicate stories, just index the one with longer content field.
2. Create a front page.
3. Add ads with jQuery script.
