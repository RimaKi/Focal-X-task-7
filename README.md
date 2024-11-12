# Focal-X-Task- 7 & 9

# project details 

- By building a task management API it includes advanced features like managing different types of tasks (Bug, Feature,
  Improvement), handling task dependencies, and analyzing performance via periodic reports. The system includes features
  like advanced authentication using JWT, protection from common security attacks, and managing user roles and
  privileges.
- The reporting system includes daily reports sent to the builder tasks about the tasks that were completed that day and
  the tasks that are yet to be completed.
- Use Job Queues to improve system performance when handling large numbers of tasks such as sending reports in email.
- There is a task dependency system where a task cannot start until all the tasks that it depends on are finished.
- Auto Reset: When you close a task that other tasks depend on, the status of the dependent tasks is automatically
  changed from Blocked to Open if all conditions are met.

# API Points:

Create a task / Update task status / Reassign a task (to change the user assigned to the task) / Add a comment to a
task /
Add an attachment to a task /
View task details /
View all tasks with advanced filters (with the ability to view pending tasks based on status): GET
/api/tasks?type=Bug&status=Open&assigned_to=2&due_date=2024-09-30&priority=High&depends_on=null
Assign a task to a user /

# Advanced Security and Protection:

- JWT Authentication: Using JWT to authenticate API to ensure high security and protection against potential attacks.
- Rate Limiting: Create a custom Rate Limiting within your application to limit the rate of requests to protect your API
  from attacks like DDoS.
- Add a set of headers to protect against different types of attacks. like : X-Powered-By , Server ,
  x-turbo-charged-by , X-Frame-Options , X-Content-Type-Options , X-Permitted-Cross-Domain-Policies , Referrer-Policy ,
  Cross-Origin-Embedder-Policy , Content-Security-Policy , X-XSS-Protection'.
- Handle attached files securely with file encryption on the server. Provide a feature to verify uploaded files (such as
  virus checking using external services if possible).

# Improve Database Performance:

- Use Caching to store frequently searched tasks to speed up responses.
- Use Database Indexing to improve the performance of search and filter queries.

# Report and handle errors:

- Use Custom Exception Handling to ensure clear error messages are presented to users.
- Implement an error handling mechanism that allows all errors that occur within the system to be logged in separate
  tables and analyzed later to improve application performance

# API Documentation:

- Comprehensive API documentation using PostMan with sample requests and responses for each API point, authentication
  details, possible error messages, examples of how to use advanced filters.

# Testing:

- The code was tested using phpunite for most of the user and task operations (the test covers 80% of the code)
- Performing an api test for many requests in postman

# Exception

    - Handle errors appropriately to ensure a good user experience. Use LOG to ensure errors can be reviewed.

# Request

    - All requests are processed within Form Request to verify their validity, organize them, and benefit from all its features as needed.

# Services

    - Services were used when needed, i.e. when there was complexity in the operations, they were moved from the
      controller to the service.

# Validations

    - Use simple and important expressions and rules in the validation process like: required , string ,
      date ,.......

# Cron job

    - Use cron job to send daily emails with copmleted tasl and uncompleted
    - Scheduling a cron job using schedule in Laravel.
    - Using Mailer for sends emails in laravel.
    - Use queue to coordinate schedule operations.

# run

## npm run dev

## php artisan serve

## php artisan queue:work

## php artisan schedule:work 
