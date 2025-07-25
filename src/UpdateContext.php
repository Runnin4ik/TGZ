<?php

declare(strict_types=1);

namespace ZhenyaGR\TGZ;

class UpdateContext
{
    private ?array $update;

    public function __construct(?array $update)
    {
        $this->update = $update;
    }

    /**
     * Фабричный метод для создания контекста из глобального входящего потока
     * (вебхук).
     */
    public static function fromWebhook(): self
    {
        $input = file_get_contents('php://input');
        $update = json_decode($input, true);
        return new self($update);
    }

    public function setUpdate(array $update)
    {
        $this->update = $update;
    }

    public function getUpdateData(): ?array
    {
        return $this->update;
    }

    public function getChatId(): ?int
    {
        return $this->update['message']['chat']['id']
            ?? $this->update['edited_message']['chat']['id']
            ?? $this->update['callback_query']['message']['chat']['id']
            ?? null;
    }

    public function getUserId(): ?int
    {
        return $this->update['message']['from']['id']
            ?? $this->update['edited_message']['from']['id']
            ?? $this->update['callback_query']['message']['from']['id']
            ?? $this->update['inline_query']['from']['id']
            ?? null;
    }

    public function getText(): ?string
    {
        return $this->update['message']['text']
            ?? $this->update['message']['caption']
            ?? $this->update['inline_query']['query']
            ?? null;
    }

    public function getMessageId(): null|int|string
    {
        return $this->update['message']['message_id'] ??
            $this->update['edited_message']['message_id'] ??
            $this->update['callback_query']['message']['message_id'] ??
            $this->update['callback_query']['inline_message_id'] ??
            null;
    }

    public function getCallbackData(): ?string
    {
        return $this->update['callback_query']['data'] ?? null;
    }

    public function getQueryId(): ?string
    {
        return $this->update['callback_query']['id']
            ?? $this->update['inline_query']['id']
            ?? null;
    }

    public function getType(): ?string
    {
        if (isset($this->update['message'])) {
            $type = (isset($this->update['message']['entities'][0]['type'])
                && $this->update['message']['entities'][0]['type']
                === 'bot_command') ? 'bot_command' : 'text';
        } elseif (isset($this->update['callback_query'])) {
            $type = 'callback_query';
        } elseif (isset($this->update['edited_message'])) {
            $type = 'edited_message';
        } elseif (isset($this->update['inline_query'])) {
            $type = 'inline_query';
        } else {
            $type = null;
        }

        return $type;
    }


}
