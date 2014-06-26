# About
**RDev** is a PHP framework that simplifies complex database interactions, object-relational mapping (ORM), and page templating.  It was written with customization, performance, security, scalability, and best-practices in mind.  Thanks to test-driven development (TDD), the framework is reliable and thoroughly tested.  However, it is still in development and should not be used in production code.  Hopefully, that will change soon.
# Table of Contents
1. [Database Connections](https://github.com/ramblingsofadev/RDev/tree/master/application/rdev/models/databases/sql)
2. [Object-Relational Mapping](https://github.com/ramblingsofadev/RDev/tree/master/application/rdev/models/orm)
3. [Query Builders](https://github.com/ramblingsofadev/RDev/tree/master/application/rdev/models/databases/sql/querybuilders)

# About the Author
I am a professional software developer and flight instructor.  I went to the University of Illinois at Urbana-Champaign and graduated with a degree in Math and Computer Science.  While in college, I obtained my commercial pilot license as well as my flight instructor licenses (CFI/CFII/MEI).  I'm active in [Angel Flight](http://angelflightcentral.org/), a charity organization that offers free flights to seriously ill people, in which I am a volunteer pilot.  When I'm not flying, you can find me playing classical piano, reading books about programming ("[Code Complete](http://www.amazon.com/Code-Complete-Practical-Handbook-Construction/dp/0735619670)" is my favorite) and non-fiction ("[T. rex and the Crater of Doom](http://www.amazon.com/Crater-Doom-Princeton-Science-Library/dp/0691131031)" and Carl Sagan's "[The Demon-Haunted World: Science as a Candle in the Dark](http://www.amazon.com/The-Demon-Haunted-World-Science-Candle/dp/0345409469)" are 2 of my favorites) , writing code, or doing something in the great outdoors.
# History
**RDev** was written by David Young.  It started as a simple exercise to write a RESTful API router in early 2014, but it quickly turned into something else.  At my 9-5 job, I was struggling with complex SQL queries that were being concatenated/Frankenstein'd together, depending on various conditions.  I decided to write "query builder" classes that could programmatically build queries for me, which greatly simplified my problem at work.  The more I worked on the simple features, the more I got interested into diving into the more complex stuff like ORM.  Before I knew it, I had developed a suite of tools, which are coming together to become the framework that is **RDev**.  Why "RDev"?  My Twitter handle is [@RamblingsOfADev](https://www.twitter.com/ramblingsofadev), which is also my GitHub name.  At first, I went with "RamODev", but I couldn't shake accidentally calling it "DevORama", so I shortened it to "RDev".
# License
This software is licensed under the MIT license.  Please read the LICENSE for more information.
# Requirements
* PHP 5.5 or higher with OpenSSL enabled
* PHPRedis
* PHPUnit