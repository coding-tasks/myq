# MyQ Cleaning Robot

### Installation

> The package is not in composer yet so if you want to install it via composer [follow these steps](https://getcomposer.org/doc/05-repositories.md#package-2).

1. Clone the repo in your project directory.
2. Run composer install (Requires php 7.1.3+).
    ```shell
    $ composer install
    ```

or if you already use docker, you can build it with docker-compose.
```shell
$ docker-compose up -d
```

Once the containers are up and running, you can either login to the bash and execute `cleaning_robot` command, or, you can directly execute it like shown below.
```shell
$ docker exec -it myq bash -c "./myq cleaning_robot ./source.json ./results.json"
```

### CLI Usage
You can use `cleaning_robot` command to run the app in cli. The command accepts source and result file.
```shell
$ ./myq cleaning_robot </path/to/source.json> </path/to/result.json>

Cleaning Robot in Action
========================

Output saved to </path/to/result.json>.
```

Help file can be accessed as below.
```shell
$ ./myq help cleaning_robot

Description:
  MyQ cleaning robot.

Usage:
  cleaning_robot <source> <result>

Arguments:
  source                Path to the source file.
  result                Path to the result file.

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  Cleans all surface in a room without manual intervention.
```

### Using as a Library
```php
$bot = new MyQ\Robot($sourcePath);

$output = $bot->run();
```

### Direction Map
| Direction | Left | Right |
| --------- | ---- | ----- |
| **East** | North | South |
| **West** | South | North |
| **North** | West | East |
| **South** | East | West |

### Example Iteration
|||||
| ---| --- | --- | --- |
| S (0, 0)  | S (0, 1)  | S (0, 2)  | S (0, 3)  |
| S (1, 0)  | S (1, 1)  | C (1, 2)  | S (1, 3)  |
| S (2, 0)  | S (2, 1)  | S (2, 2)  | S (2, 3)  |
| S (3, 0)  | null (3, 1)  | S (3, 2)  | S (3, 3)  |

**Commands:** TL, A, C, A, C, TR, A, C

- **Start:** (3, 0) facing North, battery 80
- **TL:** West, battery 79
- **A:** Obstacle, battery 77
    - **TR:** North, battery 76
    - **A:** Advance to (2, 0) facing North, battery 74
- **C:** Cleaned (2, 0), battery 69
- **A:** (1, 0) facing North, batter 67
- **C:** Cleaned (1, 0), battery 62
- **TR:** East, battery 61
- **A:** (1, 1) facing East, battery 59
- **C:** Cleaned (1, 1), battery 54

| Visited | Cleaned | Final | Battery |
| ------- | ------- | ----- | -------- |
| (3, 0), (2, 0), (1, 0), (1, 1) | (2, 0), (1, 0), (1, 1) | (1, 1) facing East | 54 |
