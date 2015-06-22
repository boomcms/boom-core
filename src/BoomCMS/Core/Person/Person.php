<?php

namespace BoomCMS\Core\Person;

use BoomCMS\Core\Group;

use Hautelook\Phpass\PasswordHash;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Auth\CanResetPassword;

class Person implements Arrayable, CanResetPassword
{
   /**
    *
    * @var array
    */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     *
     * @param Group\Group $group
     * @return Person
     */
    public function addGroup(Group\Group $group)
    {
        if ($this->loaded() && $group->loaded()) {
			DB::table('people_groups')
				->insert([
					'person_id' => $this->getId(),
					'group_id' => $group->getId(),
				]);
			
            // Inherit any roles assigned to the group.
            DB::insert('people_roles', ['person_id', 'group_id', 'role_id', 'allowed', 'page_id'])
                ->select(
                    DB::select(DB::raw($this->getId()), DB::raw($group->getId()), 'role_id', 'allowed', 'page_id')
                        ->from('group_roles')
                        ->where('group_id', '=', $group->getId())
                    )
                ->execute();
        }

        return $this;
    }

    /**
     *
     * @param string $password
     * @return boolean
     */
    public function checkPassword($password)
    {
        $hasher = new PasswordHash(8, false);

        return $hasher->checkPassword($password, $this->getPassword());
    }

    /**
     *
     * @param  type    $persistCode
     * @return boolean
     */
    public function checkPersistCode($persistCode)
    {
        return $persistCode === $this->getId();
    }

    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function getEmail()
    {
        return $this->get('email');
    }
	
	public function getEmailForPasswordReset()
	{
		return $this->getEmail();
	}

    public function getGroups()
    {
        $finder = new Group\Finder\Finder();

        return $finder
            ->addFilter(new Group\Finder\Person($this))
            ->findAll();
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getLockedUntil()
    {
        return $this->get('locked_until');
    }

    public function getLogin()
    {
        return $this->getEmail();
    }

    public function getName()
    {
        return $this->get('name');
    }

    public function getPassword()
    {
        return $this->get('password');
    }
	
	public function getRememberToken()
	{
		return $this->get('remember_token');
	}

    public function isEnabled()
    {
        return $this->get('enabled') == true;
    }

    public function isLocked()
    {
        return $this->getLockedUntil() && ($this->getLockedUntil() > time());
    }

    public function isSuperuser()
    {
        return $this->get('superuser') == true;
    }

    /**
     *
     * @return boolean
     */
    public function isValid()
    {
		return $this->loaded() && !$this->isLocked();
    }

    public function loaded()
    {
        return $this->getId() > 0;
    }

    public function loginFailed()
    {
        $this->model->set('failed_logins', ++$this->model->failed_logins);

        if ($this->model->failed_logins > 3) {
            $this->model->set('locked_until', time() + static::LOCK_WAIT);
        }

        $this->model->update();

        return $this;
    }

    /**
     *
     * @param Group $group
     * @return Person
     */
    public function removeGroup(Group\Group $group)
    {
        if ($group->loaded()) {
            $this->model->remove('groups', $group->getId());

            DB::delete('people_roles')
                ->where('group_id', '=', $group->getId())
                ->where('person_id', '=', $this->getId())
                ->execute();
        }

        return $this;
    }

    /**
	 *
	 * @param string $email
	 * @return \Boom\Person
	 */
    public function setEmail($email)
    {
        $this->data['email'] = $email;

        return $this;
    }
	
	public function setEnabled($enabled)
	{
		$this->data['enabled'] = $enabled;
		
		return $this;
	}

    /**
	 *
	 * @param string $password
	 * @return \Boom\Person
	 */
    public function setEncryptedPassword($password)
    {
        $this->data['password'] = $password;

        return $this;
    }

    /**
	 *
	 * @param string $name
	 * @return \Boom\Person
	 */
    public function setName($name)
    {
        $this->data['name'] = $name;

        return $this;
    }
	
	public function setSuperuser($superuser)
	{
		$this->data['superuser'] = $superuser;
		
		return $this;
	}

    /**
     *
     * @param string $token
     * @return \BoomCMS\Core\Person\Person
     */
    public function setRememberToken($token)
    {
        $this->data['remember_token'] = $token;

        return $this;
    }

    public function toArray()
    {
        return $this->data;
    }
}
