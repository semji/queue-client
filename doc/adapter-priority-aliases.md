# Adapter, Priority and Aliases

- [Adapters](#adapters)
- [Priority](#priority)
- [Aliases](#aliases)

## <a name="adapters"></a>Adapters

- _FileAdapter_: use file as queues system.
- _SQSAdapter_: use Amazon SQS system.
- _MemoryAdapter_: use RAM as queue system.
- _NullAdapter_: black hole queue system.

## <a name="priority"></a>Priority

For each adapter you can specify a priority handler. With a priority handler each adapter starts to get messages from highest level to lower level.
Message will be add with the **default level**.

- _StandardPriorityHandler_: Default priority handler with unique priority.
- _ThreeLevelPriorityHandler_: Priority Handler which give three priority level : "HIGH", "MEDIUM" ( **default** ), "LOW".

## <a name="aliases">Aliases

Queue client allow user to use aliases instead of real queue names.
You can use the same alias on multiple queues, so that if you add message on a single alias, each queue that belongs to this alias will receive the message.

&larr; [Usage Instructions](usage.md)