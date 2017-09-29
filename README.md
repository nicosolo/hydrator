# hydrator
## Description
Simple php hydration that support nested objects and collections

## Requirements

PHP >= 7.0

## Installation


Using composer

    composer require clea/hydrator
    

## how it's work

The hydrator base the type of property on the getter return type:

```php
public function getName(): string
{
    ...
}
```
This will simply cast the var to string.

The Transformer Class is used for cast or make operation on the value before call the setter.

You can add customs transformer functions see the example section. 

For be able to hydrate nested object you have to define the return type of the getter
```php
public function getNestedClass(): MyNestedClass
{
    ...
}
```

## Usage

### Options
```php
        $settings = [
            "additional_type" => [
               MyNumber::class => function($value){
                    return new MyNumber($value);
               }
               //...
            ],
            "cache" => true
        ];
```

### Example
```php
use Clea\Hydrator\Hydrator;

$data = [
    "string" => "value 1",
    "number" => "10",
    "notHydrated" => "test",
    "nested" => [
        "field" => "2017-10-10"
    ],
    "collection" => [
        ["field" => "10.42"],
        ["field" => "10.42"]
    ] 
];

$hydrator = new Hydrator();
$data = $this->getUserData();

$user = $hydrator->hydrate(User::class, $data);
        
```

#### Simple field casted to string 
```php
class User
{
    //...
    
    /**
     * @var string
     */
    private $string;

    /**
     * @return string
     */
    public function getString(): string
    {
        return $this->string;
    }
    
    //...
} 
```

#### Simple field casted to number 
```php
class User
{
    //...
    
    /**
     * @var int
     */
    private $number;

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }
    
    //...
} 
```


#### No hydrated field
```php
class User
{
    //...
    
    /**
    * @noHydrated
    * @var string
    */
    private $notHydrated;
    
    //...
} 
```

#### Nested field
```php
class User
{
    //...
    
    /**
     * @var Nested
     */
    private $nested;

    /**
     * @return Nested
     */
    public function getNested(): Nested
    {
        return $this->nested;
    }
    
    //...
} 
```

#### Collection field

In this case you need to use the same syntax as below, the comment are used for get the type of the entity in the collection

You need to give the full name of your class: * @var \MyProject\Message[]
```php
class User
{
    //...
    
    /**
     * @var \MyProject\UserChild[]
     */
    private $collection;

    /**
     * @return \MyProject\UserChild[]
     */
    public function getCollection(): array
    {
        return $this->collection;
    }
    
    //...
} 
```


If you want to contribute feel free to contact me / make a pull request.

The MIT License (MIT). Please see LICENSE for more information.