# justcuz
#Some of the sources used:
1. http://lornajane.net/posts/2012/building-a-restful-php-server-routing-the-request
for starter API tutorial

2. http://semantic-ui.com/
for sample UI layouts and specific component documentation

3. https://scotch.io/tutorials/easily-create-read-and-erase-cookies-with-jquery
for javascript cookie code examples

4. http://www.ugrad.cs.ubc.ca/~cs304/2016W1/tutorials/tutorials.html
for basic php to oracle connection code

#source code organization
images and catalog directories have logo and merchandise images

##on the backend side (PHP and Oracle)
justcuzsql-nov-14.sql is the file we use to define and create our database

index7.php receives API requests and sends them to the corresponding controllers

there are controllers for merchandise, user login, and retrieving member, order, and employee info

request.php (in library directory) parses requests before they are processed by controllers

some php files like action_page.php are used to process forms like order forms

##on the front-end side (HTML, jQuery, javaScript, Semantic UI)
there are html pages for the home page, order page, user views (manager, employee, customer, and member profile)
the smaller html pages have inline scripts and css rules, but the main page has separate css (main.css) and script files (client.js)

