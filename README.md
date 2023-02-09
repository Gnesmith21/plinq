# plinq
The goal of this pLinq project is to create a data access library that mimics Linq.  The basics have been created future enhancements for more flexibility and ability for DI and more will come later

to start using this library you'll want a database in which pLink can access.  To add these tables to pLink follow this pattern:

    require_once("../private/dal/index.php");
    $dal->addConnection('{name of connection}', '{server}','{username}','{password}','{database}');
    $dal->mapToConnection('{table name}', '{name of connection}');
    $dal->mapToConnection('Example', '{name of connection}');    

Now you can use the code just like linq in C#:

    $result = Example
                ::As('e')
                ->Where('`Name` LIKE '%Foo%')
                ->Select('Name')
                ->ToArray();
                
As you can see now you can compoenize your data access.  If you wanted to join all the Foo examples to Bar examples you can write something like this as well:

    $foos = Example
                ::As('e')
                ->Where("e.`Name` LIKE '%Foo%'")
                ->Select('e.Name, e.ID');
    $foosAndBars = Exmple
                    ::As('e2')
                    ->Join($foos, 'e.ID = f.ParentID', 'f')
                    ->Where("e2.`Name` LIKE '%Bar%'")
                    ->Select('e2.Name, e2.ID, e2.ParentID')
                    ->ToArray();
                    
And now $foosAndBars will have all foo related data associated to all the bar related data.

For a single record the data access can be made like this:

    $foo = Example
              ::Where("`Name` LIKE '%Foo%'")
              ->Select('*')
              ->FirstOrDefault();

To insert a record:

    $id = Example::Insert(array("Name"=>"Test","OtherData"=>"None"));

To update a record:

    Example::Where('ID = 1')->Update(array("OtherData"=>"Updated"));

OR

    Example::Update(Example::Where('ID = 1), array("OtherData"=>"Updated"));

With the last example the where statement could be used to get the data after the record is saved like so:

    $select = $Example::Where('ID = 1');
    Example::Update($select, array("OtherData"=>"Updated));
    $example = $select->FirstOrDefault();
