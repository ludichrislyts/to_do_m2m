<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */
    require_once "src/Task.php";
    $server = 'mysql:host=localhost;dbname=to_do_m2m_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);
    class TaskTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Task::deleteAll();
        }

        function test_save()
        {
            //Arrange
            $name = "Wash stuff";
            $task = new Task($name);

            //Act
            $task->save();

            //Assert
            $result = Task::getAll();
            $this->assertEquals($task, $result[0]);
        }

        function test_deleteAll()
        {
            //Arrange
            $name1 = "Wash stuff";
            $name2 = "Clean stuff";
            $task1 = new Task($name1);
            $task1->save();
            $task2 = new Task($name2);
            $task2->save();

            //Act
            Task::deleteAll();

            //Assert
            $result = Task::getAll();
            $this->assertEquals([], $result);
        }
    }
?>
