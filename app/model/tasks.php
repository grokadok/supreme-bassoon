<?php

namespace bopdev;

/**
 * 
 */
trait Tasks
{
    private function task($post)
    {
        $res['type'] = 'application/json';
        switch ($post['task']) {
            case 'weather':
                $res['content'] = round($this->weather->getWeather($post['city'])['main']['temp']);
                break;
            case 'city':
                $res['content'] = $this->maps->autoCompleteCity($post['city']);
                break;
            case 'register':
                $this->db->request([
                    'query' => 'INSERT INTO card (city,message,name) VALUES (?,?,?);',
                    'type' => 'sss',
                    'content' => [$post['city'], $post['message'], $post['name']],
                ]);
                $idcard = $this->db->request([
                    'query' => 'SELECT idcard FROM card WHERE city = ? AND message = ? AND name = ? ORDER BY created DESC LIMIT 1;',
                    'type' => 'sss',
                    'content' => [$post['city'], $post['message'], $post['name']],
                    'array' => true,
                ])[0][0];
                $res['content'] = $idcard;
                break;
        }
        return $res;
    }
}
