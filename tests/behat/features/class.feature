Feature: Classes
    In order to use the Classes
    As an administrator
    I need to be able to create a class

  Scenario: Create a class
      Given I am a platform administrator
      And I am on "/main/admin/usergroups.php?action=add"
      When I fill in the following:
          | name          | Class 1               |
          | description   | class description  |
      And I press "submit"
      Then I should see "Item added"
