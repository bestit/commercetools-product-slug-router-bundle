# Router for product slugs in a commercetools project

This router loads a product matching the given request uri to product slugs. It utilizes the cmf routing package heavily and registers the provided product router as a chained cmf router through the service tag "_router_".

## Install it

    "repositories": [
        {
            "type": "vcs",
            "url":  "git@bitbucket.org:best-it/commercetools-product-slug-router-bundle.git"
        }
    ]
    
**Then do:**

    composer require bestit/commercetools-product-slug-router-bundle

## Configuration

```
#!yaml
best_it_ct_product_slug_router:

    # Which controller-method should be used on a positive match?
    controller:           'BestIt\Frontend\ProductBundle\Controller\DetailController::indexAction'

    # Which priority has this router in the cmf chaining?
    priority:             0

    # Service id for the repositry loading products with their slug. You should fulfill the provided interface.
    repository:           ~

    # Which route name is used for a positive match?
    route:                best_it_frontend_product_detail_index
```

## Further ToDos

* The lib folder could be moved to a separate repo.
