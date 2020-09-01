<?php
    require_once dirname(__DIR__) . "/src/autoload.php";

    use Harx\Harx;
    use Harx\Loader\FilesystemLoader;

    class Article
    {
        const NAME = 'Constant Name';

        protected $title;
        protected $content;
        protected $author;
        protected $date;

        public function __construct($title, $content, $author, $date = null)
        {
            $this->title = $title;
            $this->content = $content;
            $this->author = $author;
            $this->date = $date ?: new \DateTime;
        }

        public function getTitle()
        {
            return $this->title;
        }

        public function getContent()
        {
            return $this->content;
        }

        public function getAuthor()
        {
            return $this->author;
        }

        public function getDate()
        {
            return $this->date;
        }
    }


    // simple array
    $array = array();
    $array['title'] = 'I am the walrus';
    $array['artist'] = array('name' => 'The Beatles', 'homepage' => 'http://www.thebeatles.com');

    // simple object
    $object = new stdClass;
    $object->title = 'I am the walrus';
    $object->artist = array('name' => 'The Beatles', 'homepage' => 'http://www.thebeatles.com');

    // article object
    $article = new Article('My Blog Post', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Me');

    // render template
    $harx = new Harx(new FilesystemLoader([__DIR__]));

    function hello($str) { echo "Hello ".$str; };

    // $razr->addFunction('hello', 'hello');

    echo $harx->render('template.xhtml', array(
        'name'    => 'World!',
        'pi'      => 3.14159265359,
        'number'  => -5,
        'now'     => new DateTime,
        'array'   => $array,
        'object'  => $object,
        'article' => $article
    ));
