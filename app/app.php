<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Task.php";
    require_once __DIR__."/../src/Category.php";


    $app = new Silex\Application();
    $app['debug'] = true;



    $server = 'mysql:host=localhost;dbname=to_do_m2m';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views'));

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    //HOME PAGE - DISPLAYS LIST OF taskS - OPTION TO VIEW BY task OR ADD task
    $app->get("/", function() use ($app) {
        return $app['twig']->render('index.html.twig', array('tasks' => Task::getAll(), 'categories' => Category::getAll()));
    });

    // Display Categories page - lists all categories and option to add a category
    // route = this page displays from the home page after a user clicks on the category link
    $app->get("/categories", function() use ($app) {
        return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
    });

    // Individual category page. Lists tasks associated with the category and option to add a task to
    //      the category
    // route = page displays from categories page
    $app->get('/category/{id}', function($id) use ($app){
        $category = Category::findId($id);
        if ($category !== null){
            return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks()));
        }else
            return $app['twig']->render('unauthorized.html.twig');
    });

    // page to edit a task
    // route = page displays after user clicks "edit task" from that task's page
    $app->get('/task/{id}/update', function($id) use ($app){
        $task =  Task::find($id);
        if ($task !== null){
            $task =  Task::find($id);
            // $name = $_POST['name'];
            // $task->update($name);
            return $app['twig']->render('edit_task.html.twig', array('task' => $task));
        }else{
            return $app['twig']->render('unauthorized_access.html.twig');
        }
    });

    //page that renders after an update to task, displays that tasks categorys
    $app->patch("/task/{id}", function($id) use ($app){
        $task =  Task::find($id);
        if ($task !== null){
            $name = $_POST['name'];
            //check to make sure an entry was actually made
            if (strlen($name) > 0){
                $task->update($name);
                return $app['twig']->render('categories.html.twig', array('task' => $task, 'categories' => Category::findBytaskId($id)));
            }else{
                return $app['twig']->render('error.html.twig', array('task' => $task));
            }
        }else{
            return $app['twig']->render('unauthorized_access.html.twig');
        }
    });

    // DELETE task PAGE - CONFIRMS DELETE OF task AND LINK TO HOME PAGE
    $app->delete('/task/{id}', function($id) use ($app) {
        $task =  Task::find($id);
        // check to make sure there is still a task
        if ($task !== null){
            $task->deleteOne($id);
            return $app['twig']->render('index.html.twig', array('tasks' =>  Task::getAll()));
        }else{
            return $app['twig']->render('unauthorized_access.html.twig');
        }
        //, array('tasks' => task::getAll()));
    });

    // ADDED category TO task RESULT
    $app->post('/task_categories', function() use ($app){
        $task_id = $_POST['task_id'];
        $task = Task::find($task_id);
        // check for valid entry, send task info to error page to reload task page
        if (strlen($_POST['name']) < 1){
            return $app['twig']->render('error.html.twig', array('task' => $task));
        }
        $category = new Category($_POST['name'], $task_id);
        $category->save();
        //var_dump($category);
        $task_categorys = Category::findBytaskID($task_id);
        return $app['twig']->render('categorys.html.twig', array('categorys' => $task_categorys, 'task' => Task::find($task_id)));
    });

    $app->get('/category/{id}/update', function($id) use ($app){
        $category = Category::findBycategoryId($id);
        // $name = $_POST['name'];
        // $task->update($name);
        return $app['twig']->render('edit_category.html.twig', array('category' => $category));
    });

    // UPDATE TO category
    // renders after the edit category page
    $app->patch("/category/{id}", function($id) use ($app){
        $new_name = $_POST['name'];
        $category = Category::findBycategoryId($id);
        $task_id = $category->gettaskId();
        $task = Task::find($task_id);
        //check if somehow page is reached and category is not valid
        if ($category !== null){
            // check for valid entry
            if(strlen($new_name) > 0){
                $category->setName($new_name);
                $category->update($new_name);
                // check if somehow this page is reached and task_id is not valid
                if($task !== null){
                    return $app['twig']->render('categories.html.twig', array('task' => $task, 'categories' => Category::findBytaskId($task_id)));
                // not a valid task id
                }else{
                    return $app['twig']->render('unauthorized_access.html.twig');
                }
            }else{
                return $app['twig']->render('error.html.twig', array('task' => $task));
            }
        // not a valid category id
        }else{
            return $app['twig']->render('unauthorized_access.html.twig');
        }

    });
    // DELETE category ROUTE
    $app->delete('/category/{id}', function($id) use ($app) {
        $category = Category::findBycategoryId($id);
        // check to make sure there is still a category
        if ($category !== null){
            $task_id = $category->getTaskId();
            $task = Task::find($task_id);
            $category->delete($id);
            return $app['twig']->render('categories.html.twig', array('task' => $task, 'categories' => Category::findByTaskId($task_id)));
        }else{
            return $app['twig']->render('unauthorized_access.html.twig');
        }
        //, array('tasks' => task::getAll()));
    });

    return $app;


?>
