[Documentation Home](../index.md)

# CPU Info

## Object Definition

| Property | Description | Data Type |
| --- | --- | --- |
| cpu_number | A non-negative integer identification number for the CPU represented by this object| Integer |
| usage_percentage | A float number between **0.0** and **100.0** representing the usage percentage of the CPU represented by this object. A value of **-1** or **-1.0** means that the usage percentage could not be retrieved. | Float |
| vendor | The name of the vendor that manufactured the CPU. Could be empty if the name could not be retrieved. | String |
| model | A string representing the CPU's model. Could be empty if the model could not be retrieved. | String |
| speed_mhz | A non-negative float number representing the CPU speed in MegaHertz. A value of **-1** or **-1.0** means that the CPU speed  could not be retrieved. | Float |