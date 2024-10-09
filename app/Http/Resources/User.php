<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        $m_url = str_replace('http://', '', $this->mastodon);
        $m_url = str_replace('https://', '', $m_url);
        $m = parse_url("https://".$m_url);
        $m_user = str_replace('@', '', $m['user']);
        $mastodon = "https://".$m['host']."/@".$m_user;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'mastodon' => $mastodon,
            'sendegate' => str_replace('@', '', $this->sendegate),
            'avatar' => $this->avatar,
            'search' => $this->search,
            'offer' => $this->offer,
            'projects' => Project::collection($this->projects),
        ];
    }
}
