PG Life Web Application:-	 
--------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------

PG Life is a Full-Stack Web Application. This is a project that I was assigned to make during my
Internshala Full Stack Web Development Internship Training. I got the guidance, and following that
I made this web application with my own undertsnading and knowledge. It is customized according to
what I thought would be better functionalities in this app, from the user perspective.

This web app is hosted online at:- http://debanjansarkar.epizy.com/PGLIFE/index.php

Entire web-app is fully responsive and is operational from any device.

Tech Stack:- HTML, CSS, Bootstrap 5, Javascript, AJAX, PHP, MySQL.

This web app has the following functionalities:-

1. The home page:-
--------------------
	a. Search bar, where user can enter city name(in any case), and PGs listed in that city(if exists in database), will be shown as list.
	b. Contains main cities in the form of circular sections, clicking upon which user can get the list of pgs existing in that city.


2. The PG list page:-
----------------------
	a. Shows the list of all the PGs and their main features in the selected city, in the form of beautiful cards.
	b. Filter bar, using which the PGs can be sorted according to rent and rating, in ascending or descending order.
	c. User can see here which PG is being marked interested by how many users, to know popularity.
	d. After logging in, user can mark any PG(s) as interested, from the list itself, by clicking on the heart icon.
	e. The heart icon toggles style in terms of fill color, when alternatively clicked to like or dislike the pg. Based upon click, interested user's number remains updated dynamically.
		

3. The PG details page:-
-------------------------
	a. In the property list page, if any user clicks on "View" button, that pg's entire details is being displayed in the PG details page.
	b. Images of the selected PG is being viewed at top front as a beautiful carousel.
	c. The page shows all the details such as amenities, testimonials, address of the PG neatly.
	d. User can see the selected PG is being marked interested by how many users, to know popularity.
	e. After logging in, user can mark any PG(s) as interested, from the list itself, by clicking on the heart icon.
	f. The heart icon toggles style in terms of fill color, when alternatively clicked to like or dislike the pg. Based upon click, interested user's number remains updated dynamically.


4. The dashboard:-
--------------------
	a. Appears only for the logged in users.
	b. Shows the account details of the logged in users.
	c. Below profile details, there is a section for Interested properties, which shows the cards of those PGs which the logged in user marked interested, accross any city.
	d. From this list, user can click the heart icon on any PG card, to remove that PG from interested list, and that specific page section gets dynamically changed according to user's action.


5. The Navbar:-
----------------
	a. Contains brand name.
	b. If NOT logged in, it shows option to Signup and Login.
	c. If logged in, it shows option to got to Dashboard and Logout. Also, it displays the user's first name who is being logged in currently, by using SESSION.
	d. Totally responsive toggler navbar.


6. The Breadcrumb:-
--------------------
	a. Beautify shows the relative location of the user in the web app.
	b. Contains hyperlinks to easily navigate back and forth an endpoint.


7. The Footer:-
-----------------
	a. Shows the list(containg hyperlinks) to show the list of PGs in the most popular cities.
	b. Displays copywright information.


8. Entire web app can be surfed without logging in for user's ease and attraction for new users. Only 	certain features such as dashboard, and marking interested are available upon log in.

9. Through the entire web app, each and every excetion is handled well using custom codes and UI,	such that they are easily managed, and user can get to know the fault.
