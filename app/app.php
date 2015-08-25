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

    //PAGE DISPLAYS AFTER ADDING A CATEGORY
    $app->post("/category_added", function() use ($app){
        $name = $_POST['name'];
        $new_cat = new Category($name);
        $new_cat->save();
        return $app['twig']->render('category.html.twig', array('category' => $new_cat, 'tasks' => $new_cat->getTasks()));
    });

    // Individual category page. Lists tasks associated with the category and option to add a task to
    //      the category
    // route = page displays from categories page
    $app->get("/category/{id}", function($id) use ($app){
        $category = Category::findId($id);
        if ($category !== null){
            return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks()));
        }else
            return $app['twig']->render('unauthorized_access.html.twig');
    });

    // page to edit a category
    // route = page displays after user clicks "edit category" from that category's page
    $app->get('/category/{id}/update', function($id) use ($app){
        $category =  Category::findId($id);
        if ($category !== null){
            return $app['twig']->render('edit_category.html.twig', array('category' => $category));
        }else{
            return $app['twig']->render('unauthorized_access.html.twig');
        }
    });



    // ADDED TASK TO CATEGORY RESULT
    $app->post('/task_insert', function() use ($app){
        $cat_id = $_POST['category_id'];
        $category = Category::findId($cat_id);
        // check for valid entry, send task info to error page to reload task page
        if (strlen($_POST['name']) < 1){
            return $app['twig']->render('error.html.twig', array('task' => $task));
        }
        $task = new Task($_POST['name'], $cat_id);
        $task->save();
        $category->addTask($task);
        $tasks_in_category = $category->getTasks();
        return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $tasks_in_category));
    });

    $app->patch("/category/{id}", function($id) use ($app){
        $new_name = $_POST['name'];
        $category = Category::findId($id);
        //check if somehow page is reached and category is not valid
        if ($category !== null){
            // check for valid entry
            if(strlen($new_name) > 0){
                $category->setName($new_name);
                $category->update($new_name);
                    return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks()));
            }else{
                return $app['twig']->render('error.html.twig', array('task' => $task));
            }
        // not a valid category id
        }else{
            return $app['twig']->render('unauthorized_access.html.twig');
        }

    });
    // DELETE CATEGORY ROUTE
    $app->delete('/category/{id}', function($id) use ($app) {
        $category = Category::findId($id);
        // check to make sure there is still a category
        if ($category !== null){
            // $task_ids = $category->getTasks();
            // foreach($task_ids as $task){
            //     $task = Task::find($task_id);
                $category->delete($id);
            return $app['twig']->render('categories.html.twig', array('categories' => Category::getAll()));
        }else{
            return $app['twig']->render('unauthorized_access.html.twig');
        }
        //, array('tasks' => task::getAll()));
    });
/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////     TASK ROUTES    ////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////

    //ALL TASKS PAGE
    $app->get("/tasks", function() use ($app){
        $tasks = Task::getAll();
        return $app['twig']->render("tasks.html.twig", array('tasks' => $tasks));
    });

    $app->get("/task/{id}", function($id) use ($app){
        $task = Task::findId($id);
        $categories_in_task = $task->getCategories();
        return $app['twig']->render("task.html.twig", array('task' => $task, 'categories' => $categories_in_task));
    });
    
    $app->post("/task_added", function() use ($app){
        $task = new Task($_POST['name']);
        $task->save();
        $categories = $task->getCategories();
        return $app['twig']->render("task.html.twig", array('task' => $task, 'categories'=> $categories, 'flag' => false));
    });
/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////

    //page that renders after an update to task, displays that tasks categories
    $app->patch("/task/{id}", function($id) use ($app){
        $task =  Task::findId($id);
        if ($task !== null){
            $name = $_POST['name'];
            //check to make sure an entry was actually made
            if (strlen($name) > 0){
                $task->update($name);
                return $app['twig']->render('task.html.twig', array('task' => $task, 'categories' => $task->getCategories()));
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


    $app->get('/task/{id}/update', function($id) use ($app){
        $task = Task::findId($id);
        $categories_in_task = $task->getCategories();
        return $app['twig']->render('edit_task.html.twig', array('task' => $task, 'categories' => $categories_in_task));
    });

    // UPDATE TO category
    // renders after the edit category page

    return $app;


?>
