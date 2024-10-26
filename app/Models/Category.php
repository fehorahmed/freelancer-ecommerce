<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $guarded = [];
    protected $catHierarchy = [];
    protected $catHierarchy2 = [];
    // public function product()
    // {
    //     return $this->hasMany(Product::class);
    // }

    public function children()
    {
        return $this->hasMany(Category::class,'root_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class,'root_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class);
    }

    public static function getParentCategories()
    {
        $categories = Category::where('root_id','=',1)->orderBy('id','desc')->get();
        if($categories)
        {
            return $categories;
        }
        return '';
    }
    public static function getCategoryHierarchy2($search = '')
    {
        if($search!='')
        {
            $a = Category::where('name','like', '%' . $search . '%')->get();
        }
        else{
            $a = Category::getParentCategories();
        }
        $str = "__";
        $self = new static;
        $child = [];
        $opt = [];
        if(count($a)>0)
        {
            $i = 0;
            foreach($a as $v)
            {
                $i++;
                $opt = ["id"=>$v->id,"text"=>$v->name];
                array_push($self->catHierarchy2, $opt);
                if(Category::hasChildren($v->id))
                {
                    $child = Category::getChildren2($v->id,$str);
                    if (is_array($child))
                    {
                        for ($i = 0; $i < count($child); $i++) {
                            array_push($self->catHierarchy2, $child[$i]);
                        }
                    }
                }
            }
            return $self->catHierarchy2;
        }
    }

    public static function getChildren2($id,$str)
    {
        $categories = Category::where('root_id','=',$id)->get();
        $value = [];
        $child = [];
        $opt = [];
        if(count($categories)>0)
        {
            foreach($categories as $v)
            {
                $opt = ["id"=>$v->id,"text"=>$str.' '.$v->name];
                array_push($value, $opt);

                if(Category::hasChildren($v->id))
                {
                    $child = Category::getChildren2($v->id,$str."___");
                    if (is_array($child)) {
                        for($i=0;$i<count($child);$i++) {
                            array_push($value, $child[$i]);
                        }
                    }
                }
            }
            return $value;
        }
    }
    public static function hasChildren($id)
    {
        $categories = Category::where('root_id','=',$id)->get();
        if($categories)
        {
            return true;
        }
        return false;
    }

    public static function getCategoryHierarchyAR()
    {
        $a = Category::getParentCategories();
        $str = "&nbsp;";
        $self = new static;
        $child = [];
        $data = [];
        if(count($a)>0)
        {
            $i = 0;
            foreach($a as $v)
            {
                $i++;
                $data['id'] = $v->id;
                $data['name'] = $v->name;
                $data['logo'] = $v->logo;
                $data['url'] = $v->url;
                $data['horizontal_banner'] = $v->horizontal_banner;
                $data['vertical_banner'] = $v->vertical_banner;

                if(Category::hasChildren($v->id))
                {
                    $child = Category::getChildrenAR($v->id);
                    $data['child'] = $child;
                    /*if (is_array($child))
                    {
                        for ($i = 0; $i < count($child); $i++) {
                            array_push($self->catHierarchy, $child[$i]);
                        }
                    }*/
                }
                else
                {
                    $data['child'] = [];
                }
                array_push($self->catHierarchy, $data);
            }
            return $self->catHierarchy;
        }
    }
    public static function getCategoryHierarchyForAdmin()
    {
        $a = Category::where('id',1)->get();
        $str = "&nbsp;";
        $self = new static;
        $child = [];
        $data = [];
        if(count($a)>0)
        {
            $i = 0;
            foreach($a as $v)
            {
                $i++;
                $data['id'] = $v->id;
                $data['name'] = $v->name;
                $data['logo'] = $v->logo ? asset('/') . 'storage/images/categories/logo/150/' . $v->logo : null;
                $data['url'] = $v->url;
                $data['horizontal_banner'] =$v->horizontal_banner ? asset('/') . 'storage/images/categories/banner/mobile/' . $v->horizontal_banner : null;
                $data['vertical_banner'] = $v->vertical_banner ? asset('/') . 'storage/images/categories/banner/mobile/' . $v->vertical_banner : null;

                if(Category::hasChildren($v->id))
                {
                    $child = Category::getChildrenAR($v->id);
                    $data['child'] = $child;
                    /*if (is_array($child))
                    {
                        for ($i = 0; $i < count($child); $i++) {
                            array_push($self->catHierarchy, $child[$i]);
                        }
                    }*/
                }
                else
                {
                    $data['child'] = [];
                }
                array_push($self->catHierarchy, $data);
            }
            return $self->catHierarchy;
        }
    }

    public static function getChildrenAR($id)
    {
        $categories = Category::where('root_id','=',$id)->get();
        $value = [];
        $data = [];
        $child = [];
        if(count($categories)>0)
        {
            foreach($categories as $v)
            {
                $data['id'] = $v->id;
                $data['name'] = $v->name;
                $data['url'] = $v->url;
                $data['logo'] = $v->logo ? asset('/') . 'storage/images/categories/logo/150/' . $v->logo : null;
                $data['horizontal_banner'] =$v->horizontal_banner ? asset('/') . 'storage/images/categories/banner/mobile/' . $v->horizontal_banner : null;
                $data['vertical_banner'] = $v->vertical_banner ? asset('/') . 'storage/images/categories/banner/mobile/' . $v->vertical_banner : null;

                if(Category::hasChildren($v->id))
                {
                    $child = Category::getChildrenAR($v->id);
                    $data['child'] = $child;
                    /*if (is_array($child)) {
                        for($i=0;$i<count($child);$i++) {
                            array_push($value, $child[$i]);
                        }
                    }*/
                }
                else
                {
                    $data['child'] = [];
                }
                array_push($value, $data);
            }
            return $value;
        }
    }
}
