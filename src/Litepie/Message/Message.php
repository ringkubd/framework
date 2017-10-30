<?php

namespace Litepie\Message;
use User;

class Message
{
    /**
     * $message object.
     */
    protected $message;

    /**
     * Constructor.
     */
    public function __construct(\Litepie\Message\Interfaces\MessageRepositoryInterface $message)
    {
        $this->repository = $message;
        $this->repository
            ->pushCriteria(\Litepie\Message\Repositories\Criteria\MessageResourceCriteria::class);
    }

    /**
     * Returns count of message.
     *
     * @param array $filter
     *
     * @return int
     */
    public function count($slug)
    {

        $email = user(getenv('guard'))->email;
        if($slug == 'Inbox'){
            return $this->repository->scopeQuery(function ($query) use ($slug,$email) {
                return $query->with('user')->whereStatus($slug)->whereTo($email)->where("read", "=", 0)->orderBy('id', 'DESC');
            })->count();
        }

        return $this->repository->scopeQuery(function ($query) use ($slug) {
                return $query->with('user')->whereStatus($slug)->where("read", "=", 0)->orderBy('id', 'DESC');
            })->count();
       
    }

    public function specialCount($slug)
    {

        $email = user(getenv('guard'))->email;
        $this->repository->pushCriteria(new \Litepie\Message\Repositories\Criteria\MessageUserCriteria());
        return $this->repository->scopeQuery(function ($query) use ($slug) {
                return $query->with('user')->where($slug,'=','Yes')->where("read", "=", 0)->orderBy('id', 'DESC');
            })->count();
       
    }

    public function adminMsgcount($slug)
    {
        return $this->repository->msgCount($slug);
    }

    public function adminSpecialcount($slug)
    {
        return $this->repository->specialCount($slug);
    }

    public function userMsgcount($slug, $guard)
    {
        $email = user(getenv('guard'))->email;
     
        return  $this->repository->scopeQuery(function($query)use($slug,$email){
                return $query->with('user')
                        ->where(function($qry) use($slug,$email){
                        if ($slug == 'Inbox') {
                            return $qry->whereTo($email)->where("read", "=", 0);
                        }
                        else {
                         return $qry->whereTo($email)
                            ->whereUserId(user_id('web'))
                            ->whereUserType(user_type('web'));  
                        }
                    })->whereStatus($slug);
                })->count();
           

    }

    public function userUnreadCount($slug, $guard)
    {
        return $this->repository->userUnreadCount($slug , $guard);
    }


    public function userSpecialcount($slug, $guard)
    {
        return $this->repository->userSpecialCount($slug , $guard);
    }


    /**
     * Display message of the user.
     *
     * @return void
     *
     * @author
     **/
    public function display($view)
    {
        return view('message::admin.message.' . $view);
    }

    public function messages()
    {
        return $this->repository->messages();
    }

    public function unreadCount()
    {
        return $this->repository->unreadCount();
    }

    public function unread()
    {
        return $this->repository->unread();
    }

    /**
     *Taking all Users mail id
     *@return array
     */
    public function getUsers()
    {
        $array = [];
        $model = getenv('auth.model');
        $users = $model::all();
        foreach ($users as $key => $user) {
            $array[$user->email] = $user->email;
        }
        
        return $array;
    }

}
