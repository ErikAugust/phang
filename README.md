<p>PHANG 0.5.0<br />
<br />0.5.0:<br />Major updates to Angular - moves to latest ngbp - Bootstrap 3, AngularJS ~1.2
<br />0.4.0:<br />Major updates to PHP:<br />
<br />Moves from Silex 1.* to 2.*<br />
<br />Moves from simple Silex routing to Symfony controller/routing system
<br />Updates to latest versions of Symfony components
<br />0.3.0:<br />Adds Angular Resource<br />Updates Angular UI Router from 0.0.1 to 0.2.10<br />
Updates Angular Bootstrap from 0.3.0 to 0.8.0<br /><br /> 
<br />0.2.0: PHP Unit Tests added for Base.php class</p>

<p>Example at <a href="http://phang.erikaugust.com">phang.erikaugust.com</a></p>

<p><img src="http://erikaugust.com/wp-content/uploads/2014/06/5877607_l-300x300.jpg" /></p>

<p><strong>What is heck is Phang?</strong><br />
<strong>Phang</strong> is a complete web application boilerplate.</strong>It combines the Silex PHP framework with the AngularJS JavaScript framework. Back-end concerns are handled by PHP and AngularJS handles front-end.</p>

<p><strong>Why the name Phang?</strong><br />
Two reasons:<br />1. PHP + Angular = PH-Ang.<br />
2. In earlier versions, the AngularJS interpolateProvider was changed to "{v{ }v}". They look like fangs. Since 0.2.0 has moved into the Single Page Application (SPA) format, using AngularJS for routing.</p>

<p><strong>Who is the intended user?</strong><br />
Developers who would like to use their knowledge and freedom of creating their models/classes in PHP, but want to also quickly create RESTful APIs and a front-end application framework to connect to it.</p>

<p><strong>Why should I try out Phang?</strong><br />
Phang gives you a flexible and organized way to create web applications front to back. Also, under development is the Phang command line utility which emulates some of the generator and utility functions of Ruby on Rails.</p>

<p><strong>What are the requirements</strong><br />
<p>PHP: 5.3.3+. Node.js must also be installed on your system.</p>

<p<strong>Tools/libraries used or included:</strong><br /><br />

<strong>PHP:</strong><br />
Framework: Silex<br />
Database Provider: PDO<br />
Caching Providers: Memcache, Memcache-Silex<br />
Email: swiftmailer<br />
Package Management: Composer<br />
Templating Engine: Twig<br />
Session Handling: Symfony2 Session<br />
HTTP: Symfony2 Foundation<br />
Unit Testing: PHPUnit<br />
<br /><br />

<strong>AngularJS:</strong><br />
Phang uses the fantastic <strong>ng-boilerplate</strong> for organization and tooling:<br />
Framework: AngularJS<br />
Package Management: npm, bower<br />
Unit Testing: Karma<br />
Task Running: Grunt<br />
</p>

<h3>Quick start guide</h3>

<p>PHP</p>
1. Clone into the desired directory<br />
<p><code>git clone https://github.com/ErikAugust/phang.git</code></p>

2. Update composer<br />
<p><code>cd php</code></p>
<p><code>php composer.phar self-update</code></p>

3. Delete outdated vendor files<br />
<p><code>rm -rf vendor/</code></p>

4. Update composer to create new vendor files<br />
<p><code>php composer.phar update</code></p>

5. Set your default database credentials<br />
<p><code>vi config/database.ini</code></p>

6. Create a session folder<br />
<p><code>mkdir session/</code></p>

7. Point your browser to the /test endpoint. You should see Hello world! (http://phang.erikaugust.com/test). Now point your browser to the index. You will see something like this: http://phang.erikaugust.com. All set!

<p>AngularJS</p>
1. Move to the angular directory<br />
<p><code>cd ..</code></p>
<p><code>cd angular</code></p>

2. Install Grunt-CLI, Karma and Bower
<p><code>sudo npm -g install grunt-cli karma bower</code></p>
<p><code>npm install</code></p>
<p><code>bower install</code></p>

3. Run Grunt
<p><code>grunt watch</code></p>
