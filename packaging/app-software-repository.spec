
Name: app-software-repository
Epoch: 1
Version: 1.5.10
Release: 1%{dist}
Summary: Software Repository
License: GPLv3
Group: ClearOS/Apps
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = 1:%{version}-%{release}
Requires: app-base

%description
The Software Repository app prvoides a list of repositories available to the server.  Apps available in the Marketplace are dependant on which repositories are enabled.

%package core
Summary: Software Repository - Core
License: LGPLv3
Group: ClearOS/Libraries
Requires: app-base-core
Requires: app-base-core >= 1:1.6.0

%description core
The Software Repository app prvoides a list of repositories available to the server.  Apps available in the Marketplace are dependant on which repositories are enabled.

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/software_repository
cp -r * %{buildroot}/usr/clearos/apps/software_repository/


%post
logger -p local6.notice -t installer 'app-software-repository - installing'

%post core
logger -p local6.notice -t installer 'app-software-repository-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/software_repository/deploy/install ] && /usr/clearos/apps/software_repository/deploy/install
fi

[ -x /usr/clearos/apps/software_repository/deploy/upgrade ] && /usr/clearos/apps/software_repository/deploy/upgrade

exit 0

%preun
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-software-repository - uninstalling'
fi

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-software-repository-core - uninstalling'
    [ -x /usr/clearos/apps/software_repository/deploy/uninstall ] && /usr/clearos/apps/software_repository/deploy/uninstall
fi

exit 0

%files
%defattr(-,root,root)
/usr/clearos/apps/software_repository/controllers
/usr/clearos/apps/software_repository/htdocs
/usr/clearos/apps/software_repository/views

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/software_repository/packaging
%dir /usr/clearos/apps/software_repository
/usr/clearos/apps/software_repository/deploy
/usr/clearos/apps/software_repository/language
