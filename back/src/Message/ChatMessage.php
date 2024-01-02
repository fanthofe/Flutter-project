<?php

namespace App\Message;

final class ChatMessage
{   
    /**
     * @var string
     * messange content
     */
    public $content;

    /**
     * @var string
     * author identifier
     */
    public $author;

    /**
     * @var string
     * chat identifier
     */
    public $chat;

    public function __construct(string $author, string $content, string $chat)
    {
        $this->content = $content;
        $this->author = $author;
        $this->chat = $chat;
    }
    /**
     * Get the value of content
     * @return  string
     */
    public function getContent(): string
    {
         return trim($this->content);
    }

    /**
     * Get the value of chat
     * @return string
     */
    public function getChatId(): string
    {
        $chatUrlParts = explode('/', $this->chat);
        return end($chatUrlParts);
    }

    /**
     * Get the value of author
     * @return string
     */
    public function getAuthorId(): string
    {
        $authorUrlParts = explode('/', $this->author);
        return end($authorUrlParts);
    }
}
