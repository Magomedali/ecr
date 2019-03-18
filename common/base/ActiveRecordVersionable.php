<?php

namespace common\base;

use common\modules\versionable\Versionable;
use common\modules\versionable\VersionManager;
use yii\db\Query;
use yii\db\ActiveRecord;

class ActiveRecordVersionable extends ActiveRecord implements Versionable{
	
    protected function storyActions(){

        return [
            1=>"create",
            2=>"update",
            3=>"delete"
        ];
    }

	private static $defaultStoryAttributes = [
		'type_action' => 1,
		'creator_id'=>null,
		'version'=>1,
		'entity_id'=>null
	];






    public function getStoryActions(){
        return $this->storyActions();
    }




    public function getStoryAction($code){
        $actions = $this->storyActions();
        return array_key_exists($code, $actions) ? $actions[$code] : "";
    }



    public function setStoryAttributeTypeAction($code){

        if(array_key_exists($code, $this->storyActions())){
            self::$storyAttributes["type_action"] = $code;
        }

    }



    protected static $storyAttributes = [];




    public function setStoryAttribute($key,$value){
        if(array_key_exists($key, self::$defaultStoryAttributes)){
            self::$storyAttributes[$key] = trim(strip_tags($value));
        }
    }




    public function getStoryAttribute($key){
        if(array_key_exists($key, self::$defaultStoryAttributes)){
            return self::$storyAttributes[$key];
        }
        return null;
    }





    public function getStoryAttributes(){
        $attrs = array_merge(self::$defaultStoryAttributes,self::$storyAttributes);
        
        $attrs['creator_id'] = isset(\Yii::$app->user) && !\Yii::$app->user->isGuest ? \Yii::$app->user->id : null;
        $attrs[static::resourceKey()]=  $this->id ? $this->id : null;
        $attrs['version'] = $this->lastVersionNumber;
        $attrs['created_at'] = date("Y-m-d\TH:i:s",time());

        return $attrs;
    }




	public static function versionableAttributes(){
        return [];
    } 


	/**
	* default column name for identify entity
	*/
	protected static $resourceKey = 'entity_id';


	/**
	* default column name for identify model
	*/
	protected static $primaryKeyTitle = '{{id}}';


	public  function getVersionableAttributes(){
		
		$pA = parent::getAttributes();
		
		$vA = static::versionableAttributes();
		$attrs = [];
		if(count($vA)){

			foreach ($vA as $key => $value) {
				if(array_key_exists($value, $pA)){
					$attrs[$value]=$pA[$value];
				}
			}

			
		}else{
			$attrs = $pA;
		}

		return $attrs;
	}




	/**
	* default table name for save entity history
	*/
	public static function resourceTableName(){
		
        $t = str_replace(['%','&','{','}'], '', static::tableName());
        
        return "{{%".$t."_history}}";
	}


	/**
	* @return default column name identify entity
	*/
	public static function resourceKey(){
		return static::$resourceKey;
	}


	/**
	* @return default column name identify entity
	*/
	public static function getPrimaryKeyTitle(){
		return static::$primaryKeyTitle;
	}


	/**
	* for object
	* @return default column name identify entity
	*/
	public function getResourceKey(){
		return static::resourceKey();
	}


	/**
	* @return int identifacator
	*/
	public  function getResourceId(){
		return $this::getId();
	}


	/**
	* @return table name for save entity history
	*/
	public function getResourceTable(){
		return static::resourceTableName();
	}


	/**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }


	


    /**
    * update with version
    */
    public function update($saveVersion = false,$runValidation = true, $attributeNames = null){
    	
    	$o = $this->getOldAttributes();
    	$n = $this->getAttributes();
        
        $changed = array_diff_assoc($o, $n);
        
    	if(parent::update($runValidation,$attributeNames)){
            
    		if($saveVersion && count($changed)){

    			$defA = $this->getStoryAttributes();
                $defA['version'] = $this->lastVersionNumber + 1;
	    		if($version = $this->saveHistory($defA)){
	    			//сохранить его в текущем объекте
	    			$this->setCurrentVersion($version);
	    		}
	    	}
    		return true;
    	}else{

        }
    	
    	return true;
    }


    /**
    * save with version
    */
    public function save($saveVersion = false,$runValidation = true, $attributeNames = null){
    	
    	if ($this->getIsNewRecord()) {
    		if($this->insert($runValidation, $attributeNames)){
    		
	    		if($saveVersion){
		    		if($version = $this->saveHistory()){
		    			//сохранить его в текущем объекте
		    			$this->setCurrentVersion($version);
		    		}
		    	}
	    		return true;
    		}
        }else{
            
        	return $this->update($saveVersion,$runValidation, $attributeNames);
        }
    	
    	return false;    	
    }



    public function saveHistory($defaultAttr = array()){
    	$this->beforeSaveHistory();

        $defAttr = $this->getStoryAttributes();

    	$defA = array_merge($defAttr,$defaultAttr);
        
    	$attr = $this->getVersionableAttributes();
    	
    	$params = array_merge($attr,$defA);

    	if($params && count($params)){
    		\Yii::$app->db->createCommand()->insert(self::resourceTableName(),$params)->execute();
            $version_id = \Yii::$app->db->getLastInsertID();
    		$this->afterSaveHistory();
    		return	$version_id;
    	}

    	$this->afterSaveHistory();
    	return false;
    }


    public function getLastVersion(){
    	return (new Query)->from(self::resourceTableName())->where([static::resourceKey()=>$this->getId()])->orderBy(['id' => SORT_DESC])->one();
    }


    public function getLastVersionNumber(){
    	$v = $this->getLastVersion();

    	return (int)$v['version'] ? (int)$v['version'] : 1;
    }


    
    public function getCurrentVersion(){
    	return (new Query)->from(self::resourceTableName())->where(['id'=>$this->version_id,static::resourceKey()=>$this->getId()])->one();
    }



    public function getCurrentVersionNumber(){
    	$v = $this->getCurrentVersion();

    	return (int)$v['version'] ? (int)$v['version'] : 1;
    }


    
    public function setCurrentVersion($v){
    	if($v == null) return false;

    	return \Yii::$app->db->createCommand()->update(static::tableName(), ['version_id' => (int)$v], static::getPrimaryKeyTitle() . " = " . $this->getid())->execute();
    }




    public function getHistory(){
        if(!$this->id) return false;

        return (new Query)->select(['rs.*','u.name as creator_name','u.username as creator_username'])->from(['rs'=>self::resourceTableName()])->leftJoin(['u'=>"user"]," rs.creator_id = u.id")->where([static::resourceKey()=>$this->id])->orderBy(["rs.id"=>SORT_DESC])->all();
    }




    public function delete($physical = false){
    	if($physical){
    		if($this->clearHistory()){
    			return parent::delete();
    		}
    	}else{

    		self::$defaultStoryAttributes['type_action']=3;
    		$this->isDeleted = 1;
    		return $this->save(true,false);
    	}
    }



    public function clearHistory(){
    	$command = \Yii::$app->db->createCommand();

    	return $command->delete(self::resourceTableName(),[self::resourceKey() => $this->id])->execute();
    }


    public function beforeSaveHistory(){

    }

    public function afterSaveHistory(){

    }

}

?>