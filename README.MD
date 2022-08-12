# RestClient

RestClient is an interface representing the main entry point for performing REST requests.

### Usage

#### Create model
```php
class ApiModel {
    private int $id;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
```

#### Get an object

```php
/** @var ApiModel $model */
$model = $restClient->getForObject('/api/models/:id', ApiModel::class, ['id' => 45]);

print $model->getId() . "\n";
```

#### Get a list of objects

```php
/** @var array<ApiModel> $models */
$models = $restClient->getForObject('/api/models', \RestClient\Helpers\asList(ApiModel::class));
```

