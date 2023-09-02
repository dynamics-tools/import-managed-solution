# Import a Solution to Microsoft Dynamics
This GitHub Action automates the process of importing a solution file to a Microsoft Dynamics instance. It's ideal for CI/CD pipelines where Dynamics changes need to be published automatically.

## Features
- Receives either a base64 encoded solution file or a path to a solution file in the repository.
- Imports that solution file to the Microsoft Dynamics instance.
- Supports authentication via client credentials - an application user in your Dynamics instance.

## Inputs
- `dynamics-url` - **Required**. The URL of your Dynamics instance. This is not the API URL, this is the URL you can find when you are using the application (ie -> yourorg.crm.dynamics.com not yourorg.api.crm.dynamics.com).
- `application-id` - **Required**. The Client ID of the application created in Microsoft Azure that connects to the application user
- `application-secret` - **Required**. The Client Secret of the application created in Microsoft Azure that connects to the application user
- `tenant-id` - **Required**. The Tenant ID of the application created in Microsoft Azure that connects to the application user
- `solution-file` - **Required**. The solution file to import. This can be either a base64 encoded string of the solution file or a path to the solution file.

Best practice would be holding the top four values as repository secrets and then using them as secrets instead of plain values. Here is documentation about how to use secrets in GitHub Actions: https://docs.github.com/en/actions/security-guides/encrypted-secrets

## Usage

### Add Action to Workflow

To include this action in your GitHub Workflow, add the following step:

```yaml
    - name: Publish changes to Microsoft Dynamics instance
      uses: dynamics-tools/publish-dynamics-changes@v1
      with:
        dynamics-url: 'https://example.com' # alternatively secrets.DYNAMICS_URL
        application-id: '0000-0000-0000-0000' # alternatively secrets.APPLICATION_ID
        application-secret: '.akdjfoawiefe-~kdja' # alternatively secrets.APPLICATION_SECRET
        tenant-id: '0000-0000-0000-0000' # alternatively secrets.TENANT_ID
        solution-file: 'solution.zip'
```

### Example Workflow

```yaml
name: Publish Changes

on:
  push:
    branches:
      - main

jobs:
  publish:
    runs-on: ubuntu-latest
    steps:
    - name: Checkout code
      uses: actions/checkout@v2

    - name: Export Dynamics Solution
      id: export-dynamics-solution
      uses: dynamics-tools/export-managed-solution@v1.0.0
      with:
        dynamics-url: secrets.DYNAMICS_URL
        application-id: secrets.APPLICATION_ID
        application-secret: secrets.APPLICATION_SECRET
        tenant-id: secrets.TENANT_ID
        solution-name: 'MySolution'

    - name: Publish changes to Microsoft Dynamics instance
      uses: dynamics-tools/publish-dynamics-changes@v1
      with:
        dynamics-url: secrets.STAGING_DYNAMICS_URL
        application-id: secrets.STAGING_APPLICATION_ID
        application-secret: secrets.STAGING_APPLICATION_SECRET
        tenant-id: secrets.STAGING_TENANT_ID
        solution-file: ${{ steps.export-dynamics-solution.outputs.exported_file_base64 }}
```