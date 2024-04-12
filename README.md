# COSC360-ProjectGroup888
## COSC 360 Term Project Milestone 1 - Proposal
### Group Member:
1. Lydia Chien #58850892
2. Qianyu Shang #18991588
3. Wenrui Chen #56362320
### Description
Our group decided to work on the MyDiscussionForum website that allows registered users to engage in online discussions and users without registration are only able to access read-only for the discussions. The overall platform will be similar to forums such as Reddit and HackerNews. The primary goal is to provide a space for registered users to engage in discussions by posting stories, making comments, and exchanging ideas while unregistered users will have the ability to browse and search for content but won't be able to participate actively in discussions. Additionally, the administrator can delete registered users’ posts or comments, which works like censorship.
### Requirements list

1. User Signup:

- New users can register by providing a unique username and password.

2. Login Authentication:

- Registered users can login with their created username and password.
 
3. Post Stories

- Users logged in with username and password can post any texts or a limited number of pictures with a title and are able to tag for multiple categories.
- Admin can log in and decide if deleting the posts
- Unregistered users are not allowed to do so

4. Edit profiles

- Registered users logged in can update their profiles, including profile pictures, username and password reset.

5. Comments

- Registered users can leave comments on posts.
- Unregistered users are only allowed to view the comments, so are the admins.
- The admin can also decide if deleting the comments

6. Search

- Registered, unregistered users and admin are all allowed to search users by user id or username and search posts by tags or categories.


7. Categorization

- Registered users can categorize their posts by adding tags (unregistered users can’t post).


### Additional/undecided Features
1. Notification System:
- Registered users receive notifications for comments, replies, and other relevant activities.
2. LIKE system:
- Registered users can like the posts, #thumbsup increase.
- Unregistered users and admin are not allowed to do so.

# COSC 360 MyBlogPost Project Walkthrough Testing Document

**Group Members:**
- Lydia Chien #58850892
- Qianyu Shang #18991588
- Wenrui Chen #56362320

## Purpose
This document provides step-by-step instructions for testing the ‘Bloggie’ to ensure it operates as expected and showcases its best features. It is designed for testers to easily navigate the site, execute functions, and report any issues or feedback.

## Test Users
**Admin Account Credentials:**
- **Email address:** [test@test.com](mailto:test@test.com)
- **Password:** Sat123..

**Test for Unregistered User:** The user can log into our website as a guest and will be redirected to the main page.

## Features List & Use Cases

### Registered Users (including admin) & Guests

#### 1. User Registration and Login
- Navigate to the sign-in form.
- Click on the registration link to the signup page and then create a new account.
- Fill in the registration form with the required details and submit it.
- Log in using the newly created credentials.
- **Expected Result:** Registration should be successful, and upon logging in, the user should be directed to their personal blog dashboard.

#### 2. Creating a Blog Post (Registered User)
- From the dashboard, click on 'Make Post'.
- Enter a title and content for the blog post. Upload and select corresponding images for the blog post.
- Submit the post.
- **Expected Result:** The post should be visible on the user's personal blog page and on the homepage for all users to view.

#### 3. Viewing and Searching Blog Posts (Unregistered User)
- Browse through the list of all available blog posts.
- Use the search bar to search for posts by titles.
- **Expected Result:** All posts matching the search criteria should be displayed, and unregistered users should be able to read posts but not see the comment box or editing options.

#### 4. Commenting on a Blog Post (Registered User)
- Navigate to an existing blog post.
- Enter a comment in the comment box and submit.
- **Expected Result:** The comment should appear under the blog post in real time without needing to refresh the page.

#### 5. Categorization and Filtering
- As a registered user, categorize a new blog post under a specific tag or category during creation.
- As an unregistered user, filter posts by this category.
- **Expected Result:** Only posts under the selected category should be displayed.

#### 6. Check And Edit User Profile
- After logging in successfully, the user can check their profile on the user profile page.
- The user can edit and update their information on that page.
- **Expected Results:** The user info is updated and saved in the user pool.

#### 7. Post and Comment History
- After logging in successfully, the user can check their post and comment history on the profile page.
- **Expected Results:** The profile page will generate a list of posts and comments history based on the logged-in user state.

### Additional Steps for Admin:
#### 8. Search and Delete Users (Admin accounts)
- As an admin account, the user can go to the Admin.php page.
- On the admin page, the user can search for an existing user from the shown user table, select one or more users then delete them from the database.
- **Expected Results:** The selected users are deleted from the user pool.

#### 9. Delete Posts (Admin accounts)
- As an admin account, the user can go to the Admin.php page.
- On the admin page, the user can check all of the posted blogs based on posted time in ascending order and select one or more posts to delete.
- **Expected Results:** The selected blogs are deleted from the post pool.

#### 10. Check Website Usage Statistics (Admin Account)
- As an admin account, the user can go to the Admin.php page.
- On the admin page, the user can check the usage of our website by the total blogs, users, and comments.
- **Expected Results:** The web usage report will be generated to show the admin for management purposes.

#### 11. Logout and Session Management
- As a registered user, use the logout option.
- Try to navigate back using the browser's back button.
- **Expected Result:** The user should not be able to access restricted areas and should be prompted to log in again.

## Conclusion
This walkthrough is designed to rigorously test all functionalities of the Bloggie website, from user registration to real-time comment updates. By following these detailed steps, testers can effectively identify areas for improvement and ensure a high-quality user experience for both registered and unregistered users.


