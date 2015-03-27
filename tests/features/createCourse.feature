@administration
Feature: Courses management as admin
  In order to add courses
  As an administrator
  I need to be able to create new courses from the admin page

  Scenario: See the courses list
    Given I am a platform administrator
    And I am on "/main/admin/course_list.php"
    Then I should see "Course list"
    And I should not see "not authorized"

  Scenario: See the course creation link on the courses list page
    Given I am a platform administrator
    And I am on "/main/admin/course_list.php"
    Then I should see "Create a course"

  Scenario: See the course creation link on the admin page
    Given I am a platform administrator
    And I am on "/main/admin/index.php"
    Then I should see "Create a course"

  Scenario: Access the course creation page
    Given I am a platform administrator
    And I am on "/main/admin/course_add.php"
    Then I should not see "not authorized"

  Scenario: Access the course creation page
    Given I am a platform administrator
    And I am on "/main/admin/course_add.php"
    When I fill in "title" with "TESTCOURSE1"
    And I press "submit"
    Then I should see "Course list"

  Scenario: Search and delete a course
    Given I am a platform administrator
    And I am on "/main/admin/course_list.php"
    And I fill in "course-search-keyword" with "TESTCOURSE1"
    And I press "submit"
    When I follow "Delete"
    Then I should see "Course list"
    And I should not see "not be deleted"
