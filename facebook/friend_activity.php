<?php

/**
 * Friend Activity class
 * Used to calculate a friendship score for different types of activity on facebook
 *
 * Every activity has a potential of 100 points for each friend, which is weighted against
 *  how well all of the user's friends score in that activity
 * The score is then multiplied by the weight for that activity, so that specific types
 *  of activity can factor more or less heavily into the final friendship calculation
 */

class friend_activity
{
    private $facebook_api;
    private $activity_type;
    private $weight;

    // set up the friend activity
    public function __construct($facebook_api, $activity_type, $weight = 1)
    {
        $this->facebook_api = $facebook_api;
        $this->activity_type = $activity_type;
        $this->weight = $weight;
    }

    /*
     * calculates the friend score for this type of activity
     * by calling the function for this type of activity
     * returns an array of all the friends who have a score
     */
    public function calculate()
    {
        return call_user_func('friend_activity::score_'.$this->activity_type);
    }

    private function score_relationship_status()
    {
        $user_info = $this->facebook_api->api('/me');
        $status = $user_info['relationship_status'];
        if ($status == 'Single' || $status == 'Widowed' || $status == 'Separated' || $status == 'Divorced' || empty($user_info['significant_other']) || empty($user_info['significant_other']['id']))
            return array();

        if (empty($user_info['significant_other']) || empty($user_info['significant_other']['id']))
            return array();

        // add score for the user's significant other based on the type of relationship
        $id = $user_info['significant_other']['id'];
        switch ($status)
        {
            case 'Married':
            case 'In a civil union':
                $score = 100;
                break;
            case 'Engaged':
            case 'In a domestic partnership':
                $score = 80;
                break;
            case 'In a relationship':
                $score = 50;
                break;
            case 'In an open relationship':
                $score = 40;
                break;
            case 'It\'s complicated':
                $score = 20;
                break;
            default:
                $score = 1;
                break;
        }

        // factor in for weighting
        $score *= $this->weight;

        return array($id => $score);
    }

    private function score_inbox()
    {
        return $this->messages('inbox');
    }

    private function score_outbox()
    {
        return $this->messages('outbox');
    }

    // data format is the same for inbox and outbox, so both use this function
    private function messages($type = 'inbox')
    {
        $user_id = $this->facebook_api->getUser();

        $mailbox_info = $this->facebook_api->api('/me/' . $type);
        $mailbox = $mailbox_info['data'];
        if (empty($mailbox))
            return array();

        // go through the messages in the user's mailbox and score each message
        $friend_scores = array();
        foreach ($mailbox as $message)
        {
            // score the original sender of the message
            if (!empty($message['from']['id']) && $message['from']['id'] != $user_id)
                $friend_scores[$message['from']['id']] += 5;

            // score all the friends the message was sent to
            if (!empty($message['to']) && !empty($message['to']['data']))
            {
                foreach ($message['to']['data'] as $recipient)
                {
                    if (!empty($recipient['id']) && $recipient['id'] != $user_id)
                        $friend_scores[$recipient['id']] += 2;
                }
            }

            // score the comments on the message (replies)
            if (!empty($message['comments']) && !empty($message['comments']['data']))
            {
                foreach ($message['comments']['data'] as $comment)
                {
                    if (!empty($comment['from']['id']) && $comment['from']['id'] != $user_id)
                        $friend_scores[$comment['from']['id']] += 1;
                }
            }
        }

        // prepare the final scores
        $this->normalize_scores($friend_scores);
        $this->weight_scores($friend_scores);

        return $friend_scores;
    }

    private function score_checkins()
    {
        $user_id = $this->facebook_api->getUser();

        $checkin_info = $this->facebook_api->api('/me/checkins');
        $checkins = $checkin_info['data'];
        if (empty($checkins))
            return array();

        // go through the checkin data and score it for friendship
        $friend_scores = array();
        foreach ($checkins as $checkin)
        {
            // score the friend who checked in
            if (!empty($checkin['from']['id']) && $checkin['from']['id'] != $user_id)
                $friend_scores[$checkin['from']['id']] += 5;

            // score everyone who was tagged in the checkin
            if (!empty($checkin['tags']) && !empty($checkin['tags']['data']))
            {
                foreach ($checkin['to']['data'] as $ci_friend)
                {
                    if (!empty($ci_friend['id']) && $ci_friend['id'] != $user_id)
                        $friend_scores[$ci_friend['id']] += 4;
                }
            }

            // score the comments on the checkin
            if (!empty($checkin['comments']) && !empty($checkin['comments']['data']))
            {
                foreach ($checkin['comments']['data'] as $comment)
                {
                    if (!empty($comment['from']['id']) && $comment['from']['id'] != $user_id)
                        $friend_scores[$comment['from']['id']] += 2;
                }
            }

            // score the likes on the checkin
            if (!empty($checkin['likes']) && !empty($checkin['likes']['data']))
            {
                foreach ($checkin['likes']['data'] as $like)
                {
                    if (!empty($like['id']) && $like['id'] != $user_id)
                        $friend_scores[$like['id']] += 1;
                }
            }
        }

        // prepare the final scores
        $this->normalize_scores($friend_scores);
        $this->weight_scores($friend_scores);

        return $friend_scores;
    }

    private function score_photos()
    {
        $user_id = $this->facebook_api->getUser();

        $photo_info = $this->facebook_api->api('/me/photos');
        $photos = $photo_info['data'];
        if (empty($photos))
            return array();

        // go through the photo data and score it for friendship
        $friend_scores = array();
        foreach ($photos as $photo)
        {
            // score photos posted by friends
            if (!empty($photo['from']['id']) && $photo['from']['id'] != $user_id)
                $friend_scores[$photo['from']['id']] += 5;

            // score friends tagged in the photo
            if (!empty($photo['tags']) && !empty($photo['tags']['data']))
            {
                foreach ($photo['tags']['data'] as $tag)
                {
                    if (!empty($tag['id']) && $tag['id'] != $user_id)
                        $friend_scores[$tag['id']] += 3;
                }
            }

            // score the comments on the photo
            if (!empty($photo['comments']) && !empty($photo['comments']['data']))
            {
                foreach ($photo['comments']['data'] as $comment)
                {
                    if (!empty($comment['from']['id']) && $comment['from']['id'] != $user_id)
                        $friend_scores[$comment['from']['id']] += 2;
                }
            }

            // score the likes on the photo
            if (!empty($photo['likes']) && !empty($photo['likes']['data']))
            {
                foreach ($photo['likes']['data'] as $like)
                {
                    if (!empty($like['id']) && $like['id'] != $user_id)
                        $friend_scores[$like['id']] += 1;
                }
            }
        }

        // prepare the final scores
        $this->normalize_scores($friend_scores);
        $this->weight_scores($friend_scores);

        return $friend_scores;
    }

    private function score_statuses()
    {
        return $this->calculate_comment_like_score('/me/statuses');
    }

    private function score_albums()
    {
        return $this->calculate_comment_like_score('/me/albums');
    }

    private function score_links()
    {
        return $this->calculate_comment_like_score('/me/links');
    }

    private function score_notes()
    {
        return $this->calculate_comment_like_score('/me/notes');
    }

    private function score_videos()
    {
        return $this->calculate_comment_like_score('/me/videos');
    }

    private function calculate_comment_like_score($api_path, $comment_value = 2, $like_value = 1)
    {
        $user_id = $this->facebook_api->getUser();

        $api_info = $this->facebook_api->api($api_path);
        $api_data = $api_info['data'];
        if (empty($api_data))
            return array();

        // go through the returned data and score it for friendship
        $friend_scores = array();
        foreach ($api_data as $element)
        {
            // score the comments on the data
            if (!empty($element['comments']) && !empty($element['comments']['data']))
            {
                foreach ($element['comments']['data'] as $comment)
                {
                    if (!empty($comment['from']['id']) && $comment['from']['id'] != $user_id)
                        $friend_scores[$comment['from']['id']] += $comment_value;
                }
            }

            // score the likes on the data
            if (!empty($element['likes']) && !empty($element['likes']['data']))
            {
                foreach ($element['likes']['data'] as $like)
                {
                    if (!empty($like['id']) && $like['id'] != $user_id)
                        $friend_scores[$like['id']] += $like_value;
                }
            }
        }

        // prepare the final scores
        $this->normalize_scores($friend_scores);
        $this->weight_scores($friend_scores);

        return $friend_scores;
    }

    // weights scores for all friends based on the weight of this activity
    private function weight_scores(&$friend_scores)
    {
        foreach ($friend_scores as $user_id => $user_score)
        {
            $friend_scores[$user_id] = $user_score * $this->weight;
        }
    }

    // normalize scores for all friends based on how well other friends have done in this activity
    // this gives the top scoring friend(s) a value of 100, and all other friends less
    private function normalize_scores(&$friend_scores)
    {
        $high_score = max($friend_scores);
        $offset = 100 / $high_score;

        foreach ($friend_scores as $user_id => $user_score)
        {
            $friend_scores[$user_id] = $user_score * $offset;
        }
    }
}
