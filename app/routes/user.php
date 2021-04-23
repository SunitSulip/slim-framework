use Respect\Validation\Validator as v;
// ...

$validator = new v();

$validator->addRule(v::key('username', v::allOf(
    v::notEmpty()->setTemplate('The username must not be empty'),
    v::length(3, 24)->setTemplate('Invalid length')
))->setTemplate('The key "username" is required'));

$validator->addRule(v::key('email', v::allOf(
    v::notEmpty()->setTemplate('The email must not be empty'),
    v::email()->setTemplate('Invalid email address')
))->setTemplate('The key "email" is required'));

$validator->addRule(v::key('password', v::allOf(
    v::notEmpty()->setTemplate('The password must not be empty'),
    v::length(8, 60)->setTemplate('Invalid length')
))->setTemplate('The key "password" is required'));
//...