Feature: Attendance tool

  Background:
    Given I am a platform administrator

  Scenario: Create
    Given I am on "/main/attendance/index.php?cid=1&action=attendance_add"
    Then I fill in the following:
      | title |Attendance 1|
    Then I fill in ckeditor field "description" with "Description for attendance"
    Then wait for the page to be loaded
    And I press "Save"
    And wait the page to be loaded when ready
    Then I should see "Add a date time"

  Scenario: Read
    Given I am on "/main/attendance/index.php?cid=1"
    Then I should see "Attendance 1"
    Then I follow "Attendance 1"
    Then I should see "Attendance 1"

  Scenario: Update
    Given I am on "/main/attendance/index.php?cid=1&action=attendance_edit&attendance_id=1"
    Then I should see "Edit"
    When I fill in the following:
      | title | Attendance 1 edited |
    Then I fill in ckeditor field "description" with "Description edited"
    Then I press "Update"
    Then I should see "Attendance 1 edited"

  Scenario: Delete
    Given I am on "/main/attendance/index.php?cid=1&sid=0"
    Then I should see "Attendance 1 edited"
    Then I follow "Delete"
    Then I should not see "Attendance 1 edited"
