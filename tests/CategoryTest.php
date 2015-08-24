<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */
    require_once "src/Category.php";
    require_once "src/Task.php";
    $server = 'mysql:host=localhost;dbname=to_do_m2m_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);
    class CategoryTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Category::deleteAll();
            Task::deleteAll();
        }
        function test_save()
        {
            //Arrange
            $name = "Save Stuff";
            $category = new Category($name);

            //Act
            $category->save();

            //Assert
            $result = Category::getAll();
            $this->assertEquals($category, $result[0]);
        }

        function test_deleteAll()
        {
            //Arrange
            $name1 = "Delete all stuff";
            $name2 = "Delete all more stuff";
            $category1 = new Category($name1);
            $category1->save();
            $category2 = new Category($name2);
            $category2->save();

            //Act
            Category::deleteAll();

            //Assert
            $result = Category::getAll();
            $this->assertEquals([], $result);
        }

        function test_findId()
        {
            //Arrange
            $name1 = "find id stuff";
            $name2 = "find more id stuff";
            $category1 = new Category($name1);
            $category1->save();
            $category2 = new Category($name2);
            $category2->save();

            //Act
            $id_to_find = $category2->getId();
            $result = Category::findId($id_to_find);

            //Assert
            $this->assertEquals($category2, $result);

        }

        function test_addTask()
        {
            //Arrange
            $taskname = "Category addTask";
            $test_task = new Task($taskname);
            $test_task->save();

            $category_name = "addTask stuff";
            $test_category = new Category($category_name);
            $test_category->save();

            //Act
            $test_category->addTask($test_task);

            //Assert
            $this->assertEquals($test_category->getTasks(), [$test_task]);
        }

        function test_getTasks()
        {
            //Arrange
            $taskname1 = "getTasks1";
            $test_task1 = new Task($taskname1);
            $test_task1->save();

            $taskname2 = "getTasks2";
            $test_task2 = new Task($taskname2);
            $test_task2->save();

            $category_name = "getTasks stuff";
            $test_category = new Category($category_name);
            $test_category->save();

            //Act
            $test_category->addTask($test_task1);
            $test_category->addTask($test_task2);

            //Assert
            $this->assertEquals($test_category->getTasks(), [$test_task1, $test_task2]);
        }

        function test_delete()
        {
            //Arrange
            $cat_name = "delete stuff";
            $test_category = new Category($cat_name);
            $test_category->save();

            $task_name = "delete";
            $test_task = new Task($task_name);
            $test_task->save();

            //Act
            $test_category->addTask($test_task);
            $test_category->delete();

            //Assert
            $this->assertEquals([], $test_task->getCategories());
        }

    }
?>
