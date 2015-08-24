<?php
    class Task
    {
        private $name;
        private $id;

        function __construct($name, $id = null)
        {
            $this->name = $name;
            $this->id = $id;
        }

        function getName()
        {
            return $this->name;
        }

        function setName($name)
        {
            $this->name = $name;
        }

        function getId()
        {
            return $this->id;
        }

        function setId($id)
        {
            $this->id = $id;
        }
        // save into tasks table
        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO tasks (name) VALUES ('{$this->getName()}');");
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        function addCategory($cat_to_add)
        {
            //$cat_id = $cat_to_add->getId();
            $GLOBALS['DB']->exec("INSERT INTO categories_tasks (task_id, category_id) VALUES ({$this->getId()}, {$cat_to_add->getId()});");
        }

        function getCategories()
        {
            $query = $GLOBALS['DB']->query("SELECT category_id FROM categories_tasks WHERE task_id = {$this->getId()};");
            //var_dump($query);
            $category_ids = $query->fetchAll(PDO::FETCH_ASSOC);
            //var_dump($category_ids);

            $categories = array();
            foreach($category_ids as $id) {
                $category_id = $id['category_id'];
                $result = $GLOBALS['DB']->query("SELECT * FROM categories WHERE id = {$category_id};");
                $returned_category = $result->fetchAll(PDO::FETCH_ASSOC);

                $name = $returned_category[0]['name'];
                $id = $returned_category[0]['id'];
                $new_category = new Category($name, $id);
                array_push($categories, $new_category);
            }
            return $categories;
        }
        
        function update($new_name)
        {
            $GLOBALS['DB']->exec("UPDATE tasks SET name = '{$new_name}' WHERE id = {$this->getId()};");
            $this->setDescription($new_name);
        }
        // delete task from tasks table and categories_tasks table
        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM categories_tasks WHERE task_id = {$this->getId()};");
            $GLOBALS['DB']->exec("DELETE FROM tasks WHERE id = {$this->getId()};");
        }

        //get all tasks independent of category
        static function getAll()
        {
            $returned_tasks = $GLOBALS['DB']->query("SELECT * FROM tasks");
            $tasks = array();
            foreach($returned_tasks as $task)
            {
                $name = $task['name'];
                $id = $task['id'];
                $new_task = new Task($name, $id);
                array_push($tasks, $new_task);
            }
            return $tasks;
        }

        static function findId($id)
        {
            $found_task = null;
            $tasks_to_search = Task::getAll();
            foreach ($tasks_to_search as $task)
            {
                $task_id = $task->getId();
                if ($task_id === $id)
                {
                    $found_task = $task;
                }
            }
            return $found_task;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM tasks;");
        }


    }
 ?>
