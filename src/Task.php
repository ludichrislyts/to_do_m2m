<<?php
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
                var_dump($new_task);
                array_push($tasks, $new_task);
            }
            return $tasks;
        }


    }
 ?>
