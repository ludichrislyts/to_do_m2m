<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */
    require_once "src/Task.php";
    require_once "src/Category.php";
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

        function test_findId()
        {
            //Arrange
            $name1 = "Wash stuff";
            $name2 = "Clean stuff";
            $task1 = new Task($name1);
            $task1->save();
            $task2 = new Task($name2);
            $task2->save();

            //Act
            $id_to_find = $task2->getId();
            $result = Task::findId($id_to_find);

            //Assert
            $this->assertEquals($task2, $result);

        }

        function test_addCategory()
        {
            //Arrange
            $cat_name = "Work Stuff";
            $test_category = new Category($cat_name);
            $test_category->save();

            $task_name = "program stuff";
            $test_task = new Task($task_name);
            $test_task->save();

            //Act
            $test_task->addCategory($test_category);

            //Assert
            $this->assertEquals($test_task->getCategories(), [$test_category]);
        }

        function test_getCategories()
        {
            //Arrange
            $cat_name1 = "Work Stuff";
            $test_category1 = new Category($cat_name1);
            $test_category1->save();

            $cat_name2 = "Home Stuff";
            $test_category2 = new Category($cat_name2);
            $test_category2->save();

            $task_name = "program stuff";
            $test_task = new Task($task_name);
            $test_task->save();

            //Act
            $test_task->addCategory($test_category1);
            $test_task->addCategory($test_category2);

            //Assert
            $this->assertEquals($test_task->getCategories(), [$test_category1, $test_category2]);
        }

        function test_delete()
        {
            //Arrange
            $cat_name = "Work stuff";
            $test_category = new Category($cat_name);
            $test_category->save();

            $task_name = "File reports";
            $test_task = new Task($task_name);
            $test_task->save();

            //Act
            $test_task->addCategory($test_category);
            $test_task->delete();

            //Assert
            $this->assertEquals([], $test_category->getTasks());
        }
    }
?>
