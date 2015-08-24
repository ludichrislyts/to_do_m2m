<?php
    class Category
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
        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO categories (name) VALUES ('{$this->getName()}');");
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        function getTasks()
        {
            $query = $GLOBALS['DB']->query("SELECT task_id FROM categories_tasks WHERE category_id = {$this->getId()};");
            $task_ids = $query->fetchAll(PDO::FETCH_ASSOC);

            $tasks = array();
            foreach($task_ids as $id) {
                $task_id = $id['task_id'];
                $result = $GLOBALS['DB']->query("SELECT * FROM tasks WHERE id = {$task_id};");
                $returned_task = $result->fetchAll(PDO::FETCH_ASSOC);

                $name = $returned_task[0]['name'];
                $id = $returned_task[0]['id'];
                $new_task = new Task($name, $id);
                array_push($tasks, $new_task);
            }
            return $tasks;
        }

        function addTask($task_to_add)
        {
            $task_id = $task_to_add->getId();
            $GLOBALS['DB']->exec("INSERT INTO categories_tasks (category_id, task_id) VALUES ({$this->getId()}, {$task_id});");
        }
        
        function update($new_name)
        {
            $GLOBALS['DB']->exec("UPDATE categories SET name = '{$new_name}' WHERE id = {$this->getId()};");
            $this->setName($new_name);
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM categories_tasks WHERE category_id = {$this->getId()};");
            $GLOBALS['DB']->exec("DELETE FROM categories WHERE id = {$this->getId()};");
        }


        //get all categorys independent of category
        static function getAll()
        {
            $returned_categories = $GLOBALS['DB']->query("SELECT * FROM categories");
            $categories = array();
            foreach($returned_categories as $category)
            {
                $name = $category['name'];
                $id = $category['id'];
                $new_category = new category($name, $id);
                //var_dump($new_category);
                array_push($categories, $new_category);
            }
            return $categories;
        }

        static function findId($id)
        {
            $found_category = null;
            $categories_to_search = category::getAll();
            foreach ($categories_to_search as $category)
            {
                $category_id = $category->getId();
                if ($category_id === $id)
                {
                    $found_category = $category;
                }
            }
            return $found_category;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM categories;");
        }

    }
 ?>
