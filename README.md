# **PHP-Assignment**

## **Problem Discription**
### _Simple PHP application that accepts a visitor’s email address and emails them random XKCD comics every 5 minutes._

## **Live Demo Link**
* https://assignment-rtcamp.herokuapp.com

## **Challenges**
1. Your app should include email verification to avoid people using others’ email addresses.

2. XKCD image should go as an email attachment as well as inline image content.

3. You can visit https://c.xkcd.com/random/comic/ programmatically to return a random comic URL and then use JSON API for details https://xkcd.com/json.html.

4. Please make sure your emails contain an unsubscribe link so a user can stop getting emails.
 
 ## **Solution to Challenges**
 * For Email verification I have generated **6 digit OTP** which will be provided in Email Verification mail.

 * Visited "https://xkcd.com/info.0.json" programatically to return total no. of comics.

 * Generated random comic as below: 

    >$random_comic = rand(1,$response['num']);

 * Placed that random comic number in between https://xkcd.com//info.0.json for getting json data. 

    >$comic_url = "https://xkcd.com/".$random_comic."/info.0.json";

* Fetched image url and title from the link above and attached it as attachement in email.

## **Technology Used**
* **Heroku** for hosting web application.

* **Sendgrid** for sending random comic email.

* **Remote MYSQL** for database.

* **Advanced Scheduler** for Scheduling mails every 5 minitues.

* **HTML, PHP, Javascript** for building Web Appliaction.