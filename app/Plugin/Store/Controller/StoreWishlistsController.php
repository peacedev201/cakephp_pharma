<?php 
class StoreWishlistsController extends StoreAppController
{	
    public $components = array('Paginator');

    public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);
        $this->loadModel('Store.StoreProduct');
        $this->loadModel('Store.StoreProductWishlist');
        $this->loadModel('Store.StoreSetting');
        $this->loadModel('Store.StoreProduct');
    }
    
    public function index()
    {
    }
    
    public function load_wishlist()
    {
        //wishlist
        $products = $this->StoreProductWishlist->loadWishlist($this);

        $this->set(array(
            'products' => $products,
        ));
        $this->render('Store.Elements/list/my_wishlist_list');
    }
    
    public function add_to_wishlist()
    {
        $product_id = $this->request->data['product_id'];
        $action = $this->request->data['action'];
        if(Configure::read('store.uid') == 0)
        {
            $this->_jsonError(__d('store', "Please login to continue", "Store"));
        }
        else if(!$this->StoreProduct->checkProductExist($product_id, true))
        {
            $this->_jsonError(__d('store', "Product not found", "Store"));
        }
        else if($action == 1 && $this->StoreProductWishlist->isExistInWishlist($product_id))
        {
            $this->_jsonError(__d('store', 'This product already exists in the wishlist!'));
        }
        else if($action == 0 && !$this->StoreProductWishlist->isExistInWishlist($product_id))
        {
            $this->_jsonError(__d('store', 'This product does not exist in the wishlist!'));
        }
        else 
        {
            if($action == 1 && $this->StoreProductWishlist->addToWishlist($product_id))
            {
                $this->_jsonSuccess(__d('store', 'Product was added to wishlist!'));
            }
            else if($action == 0 && $this->StoreProductWishlist->removeFromWishlist($product_id))
            {
                $this->_jsonSuccess(__d('store', 'Product was removed from wishlist!'));
            }
        }
        $this->_jsonError(__d('store', 'Something went wrong, please try again'));
    }
    
    public function remove_form_wishlist()
    {
        $product_id = $this->request->data['product_id'];
        if(!$this->StoreProduct->checkProductExist($product_id, true))
        {
            $this->_jsonError(__d('store', "Product not found", "Store"));
            exit;
        }
        else if(!$this->StoreProductWishlist->isExistInWishlist($product_id))
        {
            $this->_jsonError(__d('store', 'This product does not exist in your wishlist!'));
            exit;
        }
        else 
        {
            if($this->StoreProductWishlist->removeFromWishlist($product_id))
            {
                $this->_jsonSuccess(__d('store', 'Successfully removed.'));
            }
            $this->_jsonError(__d('store', 'Something went wrong, please try again'));
            exit;
        }
    }
}