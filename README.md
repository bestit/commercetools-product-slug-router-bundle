# Router for product slugs in a commercetools project

This router loads a product matching the given  request uri to product slugs. It utilizes the cmf routing package heavily and registers the provided product router as a chained cmf router through the service tag "_router_".

## Installation

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require bestit/commercetools-product-slug-router-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new \BestIt\CtProductSlugRouterBundle\BestItCtProductSlugRouterBundle(),
        );

        // ...
    }

    // ...
}
```

### Step 3: Configure the Bundle

```yaml
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

### Further ToDos

* The lib folder could be moved to a separate repo.
