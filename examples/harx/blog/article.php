<?php
/*
 |  HARX SIMPLE BLOG EXAMPLE
 */
    class Article {
        const POST_TYPE = "ARTICLE";

        protected $title;
        protected $content;
        protected $author;
        protected $date;

        public function __construct(array $data) {
            foreach($data AS $key => $value) {
                $this->$key = $value;
            }
        }

        public function getTitle(): string {
            return $this->title;
        }

        public function getContent(): string {
            return $this->content;
        }

        public function getAuthor(): string {
            return $this->author;
        }

        public function getDate(string $format = "d. F Y - H:i"): string {
            return date($format, strtotime($this->date));
        }
    }
